<?php

function mod_newcomments($limit, $outpost, $outer)
{
	global $wpdb;
	if (!$outer || ($outer == 0)) {
		$outer = 1111111;
	}

	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved,comment_author_email, comment_type,comment_author_url, SUBSTRING(comment_content,1,100) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_post_ID!='" . $outpost . "' AND user_id!='" . $outer . "' AND comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT $limit";
	$comments = $wpdb->get_results($sql);

	foreach ($comments as $comment ) {
		$output .= "<li><a href=\"" . get_permalink($comment->ID) . "#comment-" . $comment->comment_ID . "\" title=\"" . $comment->post_title . "上的评论\">" . _get_the_avatar($user_id = $comment->user_id, $user_email = $comment->comment_author_email) . " <strong>" . $comment->comment_author . "</strong> " . _get_time_ago($comment->comment_date_gmt) . "说：<br>" . str_replace(" src=", " data-original=", convert_smilies(strip_tags($comment->com_excerpt))) . "</a></li>";
	}

	echo $output;
}

class widget_ui_comments extends WP_Widget
{
	public function widget_ui_comments()
	{
		$widget_ops = array("classname" => "widget_ui_comments", "description" => "显示网友最新评论（头像+名称+评论）");
		$this->WP_Widget("widget_ui_comments", "D-最新评论", $widget_ops);
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters("widget_name", $instance["title"]);
		$limit = $instance["limit"];
		$outer = $instance["outer"];
		$outpost = $instance["outpost"];
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<ul>";
		echo mod_newcomments($limit, $outpost, $outer);
		echo "</ul>";
		echo $after_widget;
	}

	public function form($instance)
	{
		$defaults = array("title" => "最新评论", "limit" => 8, "outer" => "1");
		$instance = wp_parse_args((array) $instance, $defaults);
		echo "\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t标题：\r\n\t\t\t\t<input class=\"widefat\" id=\"";
		echo $this->get_field_id("title");
		echo "\" name=\"";
		echo $this->get_field_name("title");
		echo "\" type=\"text\" value=\"";
		echo $instance["title"];
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t显示数目：\r\n\t\t\t\t<input class=\"widefat\" id=\"";
		echo $this->get_field_id("limit");
		echo "\" name=\"";
		echo $this->get_field_name("limit");
		echo "\" type=\"number\" value=\"";
		echo $instance["limit"];
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t排除某用户ID：\r\n\t\t\t\t<input class=\"widefat\" id=\"";
		echo $this->get_field_id("outer");
		echo "\" name=\"";
		echo $this->get_field_name("outer");
		echo "\" type=\"number\" value=\"";
		echo $instance["outer"];
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t排除某文章ID：\r\n\t\t\t\t<input class=\"widefat\" id=\"";
		echo $this->get_field_id("outpost");
		echo "\" name=\"";
		echo $this->get_field_name("outpost");
		echo "\" type=\"number\" value=\"";
		echo $instance["outpost"];
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\r\n";
	}
}


?>
