<?php

function dtheme_readers($outer, $timer, $limit, $addlink)
{
	global $wpdb;
	$comments = $wpdb->get_results("SELECT count(comment_author) AS cnt, comment_author, comment_author_url, comment_author_email FROM " . $wpdb->comments . " WHERE comment_date > date_sub( now(), interval " . $timer . " day ) AND user_id!='1' AND comment_author!=" . $outer . " AND comment_approved='1' AND comment_type='' GROUP BY comment_author ORDER BY cnt DESC LIMIT " . $limit);

	foreach ($comments as $comment ) {
		$c_url = $comment->comment_author_url;

		if ($c_url == "") {
			$c_url = "javascript:;";
		}

		if ($addlink == "on") {
			$c_urllink = " href=\"" . $c_url . "\"";
		}
		else {
			$c_urllink = "";
		}

		$type .= "<li><a title=\"[" . $comment->comment_author . "] 近期点评" . $comment->cnt . "次\" target=\"_blank\"" . $c_urllink . ">" . _get_the_avatar($user_id = $comment->user_id, $user_email = $comment->comment_author_email) . "</a></li>";
	}

	echo $type;
}

class widget_ui_readers extends WP_Widget
{
	public function widget_ui_readers()
	{
		$widget_ops = array("classname" => "widget_ui_readers", "description" => "显示近期评论频繁的网友头像等");
		$this->WP_Widget("widget_ui_readers", "D-活跃读者", $widget_ops);
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters("widget_name", $instance["title"]);
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<ul>";
		echo dtheme_readers($outer = $instance["outer"], $timer = $instance["timer"], $limit = $instance["limit"], $addlink = $instance["addlink"]);
		echo "</ul>";
		echo $after_widget;
	}

	public function form($instance)
	{
		$defaults = array("title" => "活跃读者", "limit" => 32, "outer" => 1, "timer" => 500);
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
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t排除某人：\r\n\t\t\t\t<input class=\"widefat\" id=\"";
		echo $this->get_field_id("outer");
		echo "\" name=\"";
		echo $this->get_field_name("outer");
		echo "\" type=\"text\" value=\"";
		echo $instance["outer"];
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t几天内：\r\n\t\t\t\t<input class=\"widefat\" id=\"";
		echo $this->get_field_id("timer");
		echo "\" name=\"";
		echo $this->get_field_name("timer");
		echo "\" type=\"number\" value=\"";
		echo $instance["timer"];
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["addlink"], "on");
		echo " id=\"";
		echo $this->get_field_id("addlink");
		echo "\" name=\"";
		echo $this->get_field_name("addlink");
		echo "\">加链接\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t\r\n\r\n";
	}
}


?>
