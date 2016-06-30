<?php 

/**
* 抓取http://www.vipzhanghao.com/ 数据
*/

header("Content-type:text/html; charset=utf-8");
set_time_limit(0);

class Vipzhanghao {

	private $url;
	private $data;
	private $content;
	private $site = 'vipzhanghao';
	private $errorMax = 5; //最大错误次数
	
	public function __construct() {}
	
	public function init(){}
	
	
	public function cjSiteUrl()
	{
		$error = 0;
		
		$res = DB::fetch_first("SELECT * FROM ".DB::table('caiji')." ORDER BY id DESC LIMIT 1");
		if(preg_match('/article-(\d+)-1\.html/', $res['url'], $match)) {
			$maxid = $match[1];
			for($i=$maxid+1; $i<=$maxid+20; $i++) {
				if($error >=$this->errorMax) {
					break;
				}
				
				$requst_url = "http://www.vipzhanghao.com/article-{$i}-1.html";
				$content = dfile_get_contents($requst_url);
				
				if($content) {
					$content = mb_convert_encoding($content, 'UTF-8', 'GBK');
				}
				
				if(preg_match('/<h1 class=\"article-title\">/', $content)) {
					$this->data['url'][$requst_url] = $requst_url;
					echo "get url {$requst_url}\r\n";
				}else {
					$error += 1;
				}
				unset($content);
				usleep(5000);
			}
		}
	}
	
	
	public function cjUrl()
	{
		
		$urls = array(
			'http://www.vipzhanghao.com/iqiyi_vip/',
			'http://www.vipzhanghao.com/youku_vip/',
			'http://www.vipzhanghao.com/xunlei_vip/',
			'http://www.vipzhanghao.com/vipzhanghao/',
		);
		for($i=0; $i<count($urls); $i++) {
			$content = dfile_get_contents($urls[$i]);
			if($content) {
				$content = mb_convert_encoding($content, 'UTF-8', 'GBK');
				if(preg_match_all('/<article  class=\"excerpt\">(.*)<\/article >/iUs', $content, $list_match)) {
					foreach($list_match[1] as $key=>$val) {
						if(preg_match('/<h2><a href=\"(.*)\" target=\"_blank\">(.*)<\/a><\/h2>/', $val, $url_match)) {
							if(preg_match('/article/',$url_match[1])) {
								$url_match[1] = 'http://www.vipzhanghao.com/'.$url_match[1];
								$this->data['url'][$url_match[1]] = $url_match[1];
							}
						}
					} 
				}
			}
			unset($content);
			usleep(5000);
		}
	}
	
	public function cjContent()
	{
		/*
		$this->data = array(
			array('url'=>'http://www.vipzhanghao.com/article-7025-1.html'),
			array('url'=>'http://www.vipzhanghao.com/article-7028-1.html'),
		);
		*/
		
		$error = 0;
		$query = DB::query("SELECT * FROM ".DB::table('caiji')." WHERE site='{$this->site}' AND status=0 order by id");
		while($row = DB::fetch($query)) { 
			$data = array();
			if($error >= $this->errorMax) {
				$email_title = '采集网址内容出现错误';
				$email_content = "采集网址内容出现错误，请及时查看 {$row['url']}";
				//sendEmail('287639598@qq.com', $email_title, $email_content);
				break;
			}
		
			$html = dfile_get_contents($row['url']);
			if($html) {
				$html = mb_convert_encoding($html, 'UTF-8', 'GBK');
				
				if(preg_match('/<h1 class=\"article-title\">(.*)<\/h1>/', $html, $title_match)) {
					$data['title'] = strip_tags(trim($title_match[1]));
					$data['title'] = str_replace(array('vip账号网', 'VIP账号网', '账号网'), '', $data['title']);
				}
				
				if(preg_match('/<td id=\"article_content\" class=\"article_content\">(.*)<div id=\"diycontentbottom\" class=\"area\"><\/div>/iUs', $html, $match)) {
					
					$content_match = explode('<br>', $match[1]);
					foreach($content_match as $val2) {
						if(strpos($val2, '账号网') == false && stripos($val2, 'vip') == false && strpos($val2, '账号') && strpos($val2, '密码')) {
							$data['content'] .= $val2.'<br>';
						}
					}
					unset($content_match);
					
					if(empty($data['content']) || empty($data['title'])) {
						$error += 1;
						continue;
					}
					$data['pushtime'] = date('Y-m-d H:i:s');
					$data['status'] = 1;
					
					if($data['title']) {
					if(preg_match('/迅雷/', $data['title'])) {
						$data['cid'] = 4; 
						$data['thumbnail_id'] = 3717;
						$data['content'] = $this->getRandDesc($data['content'], 'xunlei');
					}else if(preg_match('/爱奇艺/', $data['title'])) {
						$data['cid'] = 3;
						$data['thumbnail_id'] = 3716;
						$data['content'] = $this->getRandDesc($data['content'], 'iqiyi');
					}else if(preg_match('/优酷/', $data['title'])) {
						$data['cid'] = 2;
						$data['thumbnail_id'] = 3718;
						$data['content'] = $this->getRandDesc($data['content'], 'youku');
					}else if(preg_match('/乐视/', $data['title'])) {
						$data['cid'] = 6;
						$data['thumbnail_id'] = 3721;
						$data['content'] = $this->getRandDesc($data['content'], 'qita');
					}else if(preg_match('/搜狐/', $data['title'])) {
						$data['cid'] = 6;
						$data['thumbnail_id'] = 3723;
						$data['content'] = $this->getRandDesc($data['content'], 'qita');
					}else if(preg_match('/qq/', $data['title']) || preg_match('/腾讯/', $data['title']) || preg_match('/芒果/', $data['title']) || preg_match('/暴风/', $data['title']) || preg_match('/PPTV/', $data['title'])) {
						$data['cid'] = 6;
						$data['thumbnail_id'] = 3722;
						$data['content'] = $this->getRandDesc($data['content'], 'qita');
					}
					else {
						$data['cid'] = 5;
						$data['content'] = $this->getRandDesc($data['content'], 'qita');
					}
				}
	
				//file_put_contents(ROOTPATH.'/dd.txt',$data['content']);exit;
				
				DB::query("update ".DB::table('caiji')." SET cid='{$data['cid']}',title='{$data['title']}',content='{$data['content']}',pushtime='{$data['pushtime']}',status='{$data['status']}',thumbnail_id='{$data['thumbnail_id']}' WHERE id='{$row['id']}'");
				echo "update ID: {$row['id']}\r\n";
	
				}
			}
			unset($html);
			usleep(5000);
		}
	}
	

	public function insertSiteUrl()
	{
		if($this->data['url']) {
			foreach($this->data['url'] as $key=>$val) {
				if(empty($val)) {
					continue;
				}
				if(!DB::result_first("SELECT count(*) FROM ".DB::table('caiji')." WHERE site='{$this->site}' AND url = '{$val}'")) {
					DB::query("INSERT INTO ".DB::table('caiji')." (url,site) VALUES('{$val}','{$this->site}')");
					
					$insert_id = DB::insert_id();

					echo $insert_id."\r\n";
				}
			}
		}
	}
	
	
	public function cjInsertDB()
	{
		$query = DB::query("SELECT * FROM ".DB::table('caiji')." WHERE site='{$this->site}' AND status=1 ORDER BY id desc");
		while($row = DB::fetch($query)) { 
			
			if(empty($row['content']) || empty($row['title'])) {
				continue;
			}
			
			if(!DB::result_first("SELECT count(*) FROM ".DB::table('posts')." WHERE post_name='{$row['title']}'")) {
				
				$data['post_content'] = trim($row['content']); //内容
				$data['post_title'] = htmlspecialchars(trim($row['title'])); //标题
				$data['post_date'] = date('Y-m-d H:i:s');
				$data['post_author'] = 1; //作者ID
				$data['post_excerpt'] = ''; //文章摘要
				$data['ping_status'] = 'publish';  //状态 
				$data['post_name'] = urlencode($data['post_title']);  //别名
				
				$sqlk = $sqlv = '';
				foreach($data as $key=>$val) {
					$sqlk .= "{$key},";
					$sqlv .= "'{$val}',";
				}
				$sqlk = rtrim($sqlk, ',');
				$sqlv = rtrim($sqlv, ',');
				
				DB::query("INSERT INTO ".DB::table('posts')." ({$sqlk}) VALUES({$sqlv})");
				$insert_id = DB::insert_id();
				
				DB::query("INSERT INTO ".DB::table('term_relationships')." (object_id,term_taxonomy_id) VALUES('{$insert_id}', '{$row['cid']}')");
				DB::query("INSERT INTO ".DB::table('postmeta')." (post_id,meta_key,meta_value) VALUES('{$insert_id}', '_thumbnail_id','{$row['thumbnail_id']}')");
				DB::query("UPDATE ".DB::table('term_taxonomy')." SET count=count+1 WHERE term_taxonomy_id='{$row['cid']}' limit 1");		
				
				DB::query("UPDATE ".DB::table('caiji')." SET status=2 WHERE id='{$row['id']}'");
				
				echo "insert {$insert_id}\r\n";
				sleep(1);
			}
		}
	}
	
	
	protected function getRandDesc($content, $site = 'qita')
	{
		$rand = rand(1, 4);
        if($rand == 4) {
            return $content; 
        }
		$fore = DB::fetch_first("SELECT content FROM ".DB::table('rand_desc')." WHERE site='{$site}' AND position=1 order by rand() LIMIT 1");
		$last = DB::fetch_first("SELECT content FROM ".DB::table('rand_desc')." WHERE site='all' AND position=2 order by rand() LIMIT 1");
		
		return addslashes($fore['content'])."<br><br>{$content}<br><br>".addslashes($last['content']); 
	}
	
	
	//字符编码转换
	public function diconv($data, $formcode = 'GBK', $tocode = 'UTF-8')
	{
		$data = preg_replace('/jsonp\d+\(/', '', $data);
		$data = preg_replace('/\)/', '', $data);
		return iconv($formcode, $tocode, $data);
	}

	function __destruct() {}
	
}
