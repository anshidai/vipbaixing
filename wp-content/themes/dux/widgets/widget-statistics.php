<?php

class widget_ui_statistics extends WP_Widget
{
	public function widget_ui_statistics()
	{
		$widget_ops = array("classname" => "widget_ui_statistics", "description" => "");
		$this->WP_Widget("widget_ui_statistics", "D-网站统计", $widget_ops);
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters("widget_name", $instance["title"]);
		$code = $instance["code"];
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<ul>";
		global $wpdb;

		if ($instance["post"]) {
			$count_posts = wp_count_posts();
			echo "<li><strong>日志总数：</strong>" . $count_posts->publish . "</li>";
		}

		if ($instance["comment"]) {
			$comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments");
			echo "<li><strong>评论总数：</strong>" . $comments . "</li>";
		}

		if ($instance["tag"]) {
			echo "<li><strong>标签总数：</strong>" . wp_count_terms("post_tag") . "</li>";
		}

		if ($instance["page"]) {
			$count_pages = wp_count_posts("page");
			echo "<li><strong>页面总数：</strong>" . $count_pages->publish . "</li>";
		}

		if ($instance["cat"]) {
			echo "<li><strong>分类总数：</strong>" . wp_count_terms("category") . "</li>";
		}

		if ($instance["link"]) {
			$links = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'");
			echo "<li><strong>链接总数：</strong>" . $links . "</li>";
		}

		if ($instance["user"]) {
			$users = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
			echo "<li><strong>用户总数：</strong>" . $users . "</li>";
		}

		if ($instance["last"]) {
			$last = $wpdb->get_results("SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE (post_type = 'post' OR post_type = 'page') AND (post_status = 'publish' OR post_status = 'private')");
			$last = date("Y-m-d", strtotime($last[0]->MAX_m));
			echo "<li><strong>最后更新：</strong>" . $last . "</li>";
		}

		echo "</ul>";
		echo $after_widget;
	}

	public function form($instance)
	{
		$defaults = array("title" => "网站统计", "post" => "", "comment" => "", "tag" => "", "page" => "", "cat" => "", "link" => "", "user" => "", "last" => "");
		$instance = wp_parse_args((array) $instance, $defaults);
		echo "\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t标题：\r\n\t\t\t\t<input id=\"";
		echo $this->get_field_id("title");
		echo "\" name=\"";
		echo $this->get_field_name("title");
		echo "\" type=\"text\" value=\"";
		echo $instance["title"];
		echo "\" class=\"widefat\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["post"], "on");
		echo " id=\"";
		echo $this->get_field_id("post");
		echo "\" name=\"";
		echo $this->get_field_name("post");
		echo "\">显示日志总数\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["comment"], "on");
		echo " id=\"";
		echo $this->get_field_id("comment");
		echo "\" name=\"";
		echo $this->get_field_name("comment");
		echo "\">显示评论总数\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["tag"], "on");
		echo " id=\"";
		echo $this->get_field_id("tag");
		echo "\" name=\"";
		echo $this->get_field_name("tag");
		echo "\">显示标签总数\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["page"], "on");
		echo " id=\"";
		echo $this->get_field_id("page");
		echo "\" name=\"";
		echo $this->get_field_name("page");
		echo "\">显示页面总数\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["cat"], "on");
		echo " id=\"";
		echo $this->get_field_id("cat");
		echo "\" name=\"";
		echo $this->get_field_name("cat");
		echo "\">显示分类总数\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["link"], "on");
		echo " id=\"";
		echo $this->get_field_id("link");
		echo "\" name=\"";
		echo $this->get_field_name("link");
		echo "\">显示链接总数\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["user"], "on");
		echo " id=\"";
		echo $this->get_field_id("user");
		echo "\" name=\"";
		echo $this->get_field_name("user");
		echo "\">显示用户总数\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["last"], "on");
		echo " id=\"";
		echo $this->get_field_id("last");
		echo "\" name=\"";
		echo $this->get_field_name("last");
		echo "\">显示最后更新\r\n\t\t\t</label>\r\n\t\t</p>\r\n";
	}
}


?>
