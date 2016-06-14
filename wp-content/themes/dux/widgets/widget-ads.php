<?php

class widget_ui_ads extends WP_Widget
{
	public function widget_ui_ads()
	{
		$widget_ops = array("classname" => "widget_ui_ads", "description" => "显示一个广告(包括富媒体)");
		$this->WP_Widget("widget_ui_ads", "D-广告", $widget_ops);
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters("widget_name", $instance["title"]);
		$code = $instance["code"];
		echo $before_widget;
		echo "<div class=\"item\">" . $code . "</div>";
		echo $after_widget;
	}

	public function form($instance)
	{
		$defaults = array("title" => "广告", "code" => "<a href=\"http://www.ymjihe.com/\"><img src=\"http://www.daqianduan.com/wp-content/uploads/2015/01/asb-01.jpg\"></a>");
		$instance = wp_parse_args((array) $instance, $defaults);
		echo "\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t广告名称：\r\n\t\t\t\t<input id=\"";
		echo $this->get_field_id("title");
		echo "\" name=\"";
		echo $this->get_field_name("title");
		echo "\" type=\"text\" value=\"";
		echo $instance["title"];
		echo "\" class=\"widefat\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t广告代码：\r\n\t\t\t\t<textarea id=\"";
		echo $this->get_field_id("code");
		echo "\" name=\"";
		echo $this->get_field_name("code");
		echo "\" class=\"widefat\" rows=\"12\" style=\"font-family:Courier New;\">";
		echo $instance["code"];
		echo "</textarea>\r\n\t\t\t</label>\r\n\t\t</p>\r\n";
	}
}


?>
