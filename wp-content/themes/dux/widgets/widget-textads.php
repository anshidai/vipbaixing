<?php

class widget_ui_textads extends WP_Widget
{
	public function widget_ui_textads()
	{
		$widget_ops = array("classname" => "widget_ui_textasb", "description" => "显示一个文本特别推荐");
		$this->WP_Widget("widget_ui_textads", "D-特别推荐", $widget_ops);
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters("widget_name", $instance["title"]);
		$tag = $instance["tag"];
		$content = $instance["content"];
		$link = $instance["link"];
		$style = $instance["style"];
		$blank = $instance["blank"];
		$lank = "";

		if ($blank) {
			$lank = " target=\"_blank\"";
		}

		echo $before_widget;
		echo "<a class=\"" . $style . "\" href=\"" . $link . "\"" . $lank . ">";
		echo "<strong>" . $tag . "</strong>";
		echo "<h2>" . $title . "</h2>";
		echo "<p>" . $content . "</p>";
		echo "</a>";
		echo $after_widget;
	}

	public function form($instance)
	{
		$defaults = array("title" => "DUX主题 新一代主题", "tag" => "吐血推荐", "content" => "DUX Wordpress主题是大前端当前使用主题，是大前端积累多年Wordpress主题经验设计而成；更加扁平的风格和干净白色的架构会让网站显得内涵而出色...", "link" => "http://www.ymjihe.com/", "style" => "style02");
		$instance = wp_parse_args((array) $instance, $defaults);
		echo "\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t名称：\r\n\t\t\t\t<input id=\"";
		echo $this->get_field_id("title");
		echo "\" name=\"";
		echo $this->get_field_name("title");
		echo "\" type=\"text\" value=\"";
		echo $instance["title"];
		echo "\" class=\"widefat\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t描述：\r\n\t\t\t\t<textarea id=\"";
		echo $this->get_field_id("content");
		echo "\" name=\"";
		echo $this->get_field_name("content");
		echo "\" class=\"widefat\" rows=\"3\">";
		echo $instance["content"];
		echo "</textarea>\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t标签：\r\n\t\t\t\t<input id=\"";
		echo $this->get_field_id("tag");
		echo "\" name=\"";
		echo $this->get_field_name("tag");
		echo "\" type=\"text\" value=\"";
		echo $instance["tag"];
		echo "\" class=\"widefat\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t链接：\r\n\t\t\t\t<input style=\"width:100%;\" id=\"";
		echo $this->get_field_id("link");
		echo "\" name=\"";
		echo $this->get_field_name("link");
		echo "\" type=\"url\" value=\"";
		echo $instance["link"];
		echo "\" size=\"24\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t样式：\r\n\t\t\t\t<select style=\"width:100%;\" id=\"";
		echo $this->get_field_id("style");
		echo "\" name=\"";
		echo $this->get_field_name("style");
		echo "\" style=\"width:100%;\">\r\n\t\t\t\t\t<option value=\"style01\" ";
		selected("style01", $instance["style"]);
		echo ">蓝色</option>\r\n\t\t\t\t\t<option value=\"style02\" ";
		selected("style02", $instance["style"]);
		echo ">橘红色</option>\r\n\t\t\t\t\t<option value=\"style03\" ";
		selected("style03", $instance["style"]);
		echo ">绿色</option>\r\n\t\t\t\t\t<option value=\"style04\" ";
		selected("style04", $instance["style"]);
		echo ">紫色</option>\r\n\t\t\t\t\t<option value=\"style05\" ";
		selected("style05", $instance["style"]);
		echo ">青色</option>\r\n\t\t\t\t</select>\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t<input style=\"vertical-align:-3px;margin-right:4px;\" class=\"checkbox\" type=\"checkbox\" ";
		checked($instance["blank"], "on");
		echo " id=\"";
		echo $this->get_field_id("blank");
		echo "\" name=\"";
		echo $this->get_field_name("blank");
		echo "\">新打开浏览器窗口\r\n\t\t\t</label>\r\n\t\t</p>\r\n";
	}
}


?>
