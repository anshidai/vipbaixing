<?php 

/**
* 抓取http://www.vipbuluo.com/ 数据
*/

header("Content-type:text/html; charset=utf-8");
set_time_limit(0);

class Vipbuluo {

	private $url;
	private $data;
	private $content;
	private $site = 'vipbuluo';
	private $errorMax = 5; //最大错误次数
	
	public function __construct() {}
	
	public function init(){}
	
	public function cjSiteUrl()
	{
		$error = 0;
		
		$res = DB::fetch_first("SELECT * FROM ".DB::table('caiji')." ORDER BY id DESC LIMIT 1");
		if(preg_match('/post\/(\d+)\.html/', $res['url'], $match)) {
			$maxid = $match[1];
			for($i=$maxid+1; $i<=$maxid+50; $i++) {
				if($error >=$this->errorMax) {
					break;
				}
				
				$requst_url = "http://www.vipbuluo.com/post/{$i}.html";
				
				$content = dfile_get_contents($requst_url);
				if(stripos($content, $requst_url)) {
					$this->data[]['url'] = $requst_url;
					echo "get url {$requst_url}\r\n";
				}else {
					$error += 1;
				}
				unset($content);
			}
		}
	}
	
	private function parseXml()
	{
		if($this->content) {
			$xml_content = simplexml_load_string($this->content);
			if($xml_content) {
				foreach($xml_content as $val) {
					if(strpos($val->loc, 'post')>0) { 
						$this->data[]['url'] = (string)$val->loc;
					}
				}
			}
			unset($xml_content);
			return $this->data;
		}
	}
	
	public function cjXml($url)
	{
		$this->url = $url;
		
		$this->content = dfile_get_contents($this->url);
		$this->parseXml($this->content);
		unset($this->content);
	}
	
	public function insertSiteUrl()
	{
		if($this->data) {
			foreach($this->data as $key=>$val) {
				if(!DB::result_first("SELECT count(*) FROM ".DB::table('caiji')." WHERE site='{$this->site}' AND url = '{$val['url']}'")) {
					DB::query("INSERT INTO ".DB::table('caiji')." (url,site) VALUES('{$val['url']}','{$this->site}')");
					
					$insert_id = DB::insert_id();

					echo $insert_id."\r\n";
				}
			}
		}
	}
	
	public function cjContent()
	{
		
		$error = 0;
		
		$query = DB::query("SELECT * FROM ".DB::table('caiji')." WHERE site='{$this->site}' AND status=0 order by id");
		while($row = DB::fetch($query)) {  
			
			if($error >= $this->errorMax) {
				$email_title = '采集网址内容出现错误';
				$email_content = "采集网址内容出现错误，请及时查看 {$row['url']}";
				sendEmail('287639598@qq.com', $email_title, $email_content);
				break;
			}
			
			$html = dfile_get_contents($row['url']);
			if($html) {
				if(preg_match('/<dl id=\"title\"><h1>(.*)<\/h1>/', $html, $title_match)) {
					$data['title'] = strip_tags(trim($title_match[1]));
					$data['title'] = str_replace(array('老冰棍分享网','vip部落账号网','老冰棍','VIP部落','vip部落','VIP分享网','vip部落分享网','vip账号网'), 'vip百姓网', $data['title']);
				}
				if(preg_match('/<p>(.*)\| 作者/', $html, $pushtime_match)) {
					$data['pushtime'] = strip_tags(trim($pushtime_match[1]));
				}

				$data['content'] = pos_html('<dl id="zi">', '<span style="font-size: 16px;">　　<strong>【一号当铺】 微信号', $html);
				if($data['content']) {
					$data['content'] = preg_replace('/<img[^>]+>/i', '', $data['content']);
					$data['content'] = str_replace(array("<dl id=\"zi\">", '<p></p>'), '', $data['content']);
					$data['content'] = str_replace(array('vip部落分享网','vip部落账号网','VIP分享网','vip部落','VIP部落','vip账号网'), 'vip百姓网', $data['content']);
					$data['content'] = str_replace('请各位朋友尊重我们的劳动成果，不要分享转载抄袭本站账号，让我们有更加充足的激情为大家继续服务下去，做得更好哦！', '', $data['content']);
					$data['content'] = trim($data['content']);
					
					if(preg_match('/<p>$/', $data['content'])) {
						$data['content'] = preg_replace('/<p>$/', '', $data['content']);
					}
				}
				
				if(empty($data['content']) && $error>5) {
					$error += 1;
					continue;
				}
				
				if($data['title']) {
					if(strpos($data['title'], '迅雷')) {
						$data['cid'] = 4; 
						$data['thumbnail_id'] = 3717;
					}else if(strpos($data['title'], '爱奇艺')) {
						$data['cid'] = 3;
						$data['thumbnail_id'] = 3716;
					}else if(strpos($data['title'], '优酷')) {
						$data['cid'] = 2;
						$data['thumbnail_id'] = 3718;
					}else if(strpos($data['title'], '乐视')) {
						$data['cid'] = 6;
						$data['thumbnail_id'] = 3721;
					}else if(strpos($data['title'], '搜狐')) {
						$data['cid'] = 6;
						$data['thumbnail_id'] = 3723;
					}else if(stripos($data['title'], 'qq') || strpos($data['title'], '腾讯')) {
						$data['cid'] = 6;
						$data['thumbnail_id'] = 3722;
					}
					else {
						$data['cid'] = 5;
					}
				}
				
				$data['status'] = 1;
				
				DB::query("update ".DB::table('caiji')." SET cid='{$data['cid']}',title='{$data['title']}',content='{$data['content']}',pushtime='{$data['pushtime']}',status='{$data['status']}',thumbnail_id='{$data['thumbnail_id']}' WHERE id='{$row['id']}'");
				echo "update ID: {$row['id']}\r\n";
			}
			unset($html);
		}
	}
	
	public function cjInsertDB()
	{
		$query = DB::query("SELECT * FROM ".DB::table('caiji')." WHERE site='{$this->site}' AND status=1 ORDER BY id desc");
		while($row = DB::fetch($query)) { 
			
			if(!DB::result_first("SELECT count(*) FROM ".DB::table('posts')." WHERE post_name='{$row['title']}'")) {
				
				$data['post_content'] = trim($row['content']); //内容
				$data['post_title'] = htmlspecialchars(trim($row['title'])); //标题
				$data['post_date'] = trim($row['pushtime']);
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
			}
		}
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
