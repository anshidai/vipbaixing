<?php

function dd_sticky_posts_list($limit, $img)
{
	$sticky = get_option("sticky_posts");
	rsort($sticky);
	$args = array("post__in" => $sticky, "showposts" => $limit, "ignore_sticky_posts" => 1);
	query_posts($args);

	while (have_posts()) {
		the_post();
		echo "<li><a";
		echo _post_target_blank();
		echo " href=\"";
		the_permalink();
		echo "\">";

		if ($img) {
			echo "<span class=\"thumbnail\">";
			echo _get_post_thumbnail();
			echo "</span>";
		}
		else {
			$img = "";
		}

		echo "<span class=\"text\">";
		the_title();
		echo "</span><span class=\"muted\">";
		the_time("Y-m-d");
		echo "</span><span class=\"muted\">";
		echo "评论(";
		echo comments_number("", "1", "%");
		echo ")";
		echo "</span></a></li>\r\n";
	}

	wp_reset_query();
}

class widget_ui_sticky extends WP_Widget
{
	public function widget_ui_sticky()
	{
		$widget_ops = array("classname" => "widget_ui_posts", "description" => "图文展示");
		$this->WP_Widget("widget_ui_sticky", "D-置顶推荐", $widget_ops);
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters("widget_name", $instance["title"]);
		$limit = $instance["limit"];
		$img = $instance["img"];
		$style = "";

		if (!$img) {
			$style = " class=\"nopic\"";
		}

		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<ul" . $style . ">";
		echo dd_sticky_posts_list($limit, $img);
		echo "</ul>";
		echo $after_widget;
	}

	public function form($instance)
	{
		$defaults = array("title" => "置顶推荐", "limit" => 6, "img" => "");
		$instance = wp_parse_args((array) $instance, $defaults);
		echo "\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t标题：\r\n\t\t\t\t<input style=\"width:100%;\" id=\"";
		echo $this->get_field_id("title");
		echo "\" name=\"";
		echo $this->get_field_name("title");
		echo "\" type=\"text\" value=\"";
		echo $instance["title"];
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t显示数目：\r\n\t\t\t\t<input style=\"width:100%;\" id=\"";
		echo $this->get_field_id("limit");
		echo "\" name=\"";
		echo $this->get_field_name("limit");
		echo "\" type=\"number\" value=\"";
		echo $instance["limit"];
		echo "\" size=\"24\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["img"], "on");
		echo " id=\"";
		echo $this->get_field_id("img");
		echo "\" name=\"";
		echo $this->get_field_name("img");
		echo "\">显示图片\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t\r\n\t";
	}
}


?>
