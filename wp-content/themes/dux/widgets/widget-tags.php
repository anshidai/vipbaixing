<?php

class widget_ui_tags extends WP_Widget
{
	public function widget_ui_tags()
	{
		$widget_ops = array("classname" => "widget_ui_tags", "description" => "显示热门标签");
		$this->WP_Widget("widget_ui_tags", "D-标签云", $widget_ops);
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters("widget_name", $instance["title"]);
		$count = $instance["count"];
		$offset = $instance["offset"];
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<div class=\"items\">";
		$tags_list = get_tags("orderby=count&order=DESC&number=" . $count . "&offset=" . $offset);

		if ($tags_list) {
			foreach ($tags_list as $tag ) {
				echo "<a href=\"" . get_tag_link($tag) . "\">" . $tag->name . " (" . $tag->count . ")</a>";
			}
		}
		else {
			echo "暂无标签！";
		}

		echo "</div>";
		echo $after_widget;
	}

	public function form($instance)
	{
		$defaults = array("title" => "热门标签", "count" => 30, "offset" => 0);
		$instance = wp_parse_args((array) $instance, $defaults);
		echo "\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t名称：\r\n\t\t\t\t<input id=\"";
		echo $this->get_field_id("title");
		echo "\" name=\"";
		echo $this->get_field_name("title");
		echo "\" type=\"text\" value=\"";
		echo $instance["title"];
		echo "\" class=\"widefat\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t显示数量：\r\n\t\t\t\t<input id=\"";
		echo $this->get_field_id("count");
		echo "\" name=\"";
		echo $this->get_field_name("count");
		echo "\" type=\"number\" value=\"";
		echo $instance["count"];
		echo "\" class=\"widefat\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t去除前几个：\r\n\t\t\t\t<input id=\"";
		echo $this->get_field_id("offset");
		echo "\" name=\"";
		echo $this->get_field_name("offset");
		echo "\" type=\"number\" value=\"";
		echo $instance["offset"];
		echo "\" class=\"widefat\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t\r\n";
	}
}


?>
