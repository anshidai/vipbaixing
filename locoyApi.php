<?php

include "./wp-config.php";

$act = $_GET['act']? $_GET['act']: 'list';

if(!in_array($act, array('save', 'list'))) {
	die('no found');
}

if($act == 'list') {
	$cats = get_categories("hierarchical=0&hide_empty=0");
	if($cats) {
		echo "<select name=\"cateid\">\n";
		foreach($cats as $val) {
			$val = (array)$val;
			echo "<option value=\"{$val['term_id']}\">{$val['name']}</option>";
		}
		echo "</select>";
	}
}else {
	
	$post = $_POST;
	
	//$post['post_content'] = htmlspecialchars(trim($post['post_content']));
	$post['post_content'] = trim($post['post_content']);
	$post['post_title'] = htmlspecialchars(trim($post['post_title']));
	$post['post_date'] = trim($post['post_date']);
	
	
	/*
	if(preg_match('/\((\d+-\d+)\)/', $post['post_date'], $match)) {
		$post['post_date'] = $match[1];
	}
	
	else if(strpos($post['post_date'], '天前')>0) {
		if(preg_match('/(\d+)天前/', $post['post_date'], $match)) {
			$post['post_date'] = date('m-d', strtotime("-{$match[1]} days"));
		}
	}
	else if(strpos($post['post_date'], '小时前')>0) {
		$post['post_date'] = date('m-d');
		
	}else {
		$post['post_date'] = date('m-d');
	}
	
	if($hours = parser_time_hours($post['post_title'])) {
		$post['post_date'] = '2016-'.$post['post_date'] . " {$hours}:".date('i:s');
	}else {
		$post['post_date'] = '2016-' .$post['post_date'] .date(' H:i:s');
	}
	*/
	$post['post_date'] = $post['post_date'] .date(' H:i:s');

	$data['post_author'] = 1; //作者ID
	$data['post_date'] = $post['post_date']; //时间
	$data['post_date_gmt'] = $data['post_date']; //GMT(格林威治)时间
	$data['post_content'] = $post['post_content'];  //内容
	$data['post_title'] = $post['post_title']; //标题
	$data['post_excerpt'] = ''; //文章摘要
	$data['ping_status'] = 'publish';  //状态 
	$data['post_name'] = urlencode($post['post_title']);  //别名
	$data['to_ping'] = ''; //强制该文章去ping某个URI

	$data['guid'] = 0; //默认文章url地址
	
	if($wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_name = '{$data['post_name']}'")) {
		echo '已存在重复标题';
	}else {
		if($wpdb->insert($wpdb->posts, $data)) {
			$insert_id = $wpdb->insert_id;
			
			$wpdb->insert($wpdb->term_relationships, array('object_id'=>$insert_id, 'term_taxonomy_id'=>$post['post_category']));
			
			$taxonomy = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = %d", $post['post_category']));

			if($taxonomy) {
				$taxonomy = (array)$taxonomy;
				$wpdb->update($wpdb->term_taxonomy, array('count'=>$taxonomy['count']+1), array('term_taxonomy_id'=>$post['post_category']));
			}
			echo '发布成功';
		}
	}
	
	
}


function parser_time_hours($title)
{
	$hours = '';

	for($i=24; $i>=1; $i--) {
		if(strpos($title, "{$i}点") >0) {
			$hours = $i;
			break;
		}
	}
	return $hours;
	
	
	
}




