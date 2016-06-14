<?php

function dtheme_posts_list($orderby, $limit, $cat, $img)
{
	$args = array(
		"order"               => "DESC",
		"cat"                 => $cat,
		"orderby"             => $orderby,
		"showposts"           => $limit,
		"category__not_in"    => array(211),
		"ignore_sticky_posts" => 1
		);
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

class widget_ui_posts extends WP_Widget
{
	public function widget_ui_posts()
	{
		$widget_ops = array("classname" => "widget_ui_posts", "description" => "图文展示（最新文章+热门文章+随机文章）");
		$this->WP_Widget("widget_ui_posts", "D-聚合文章", $widget_ops);
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters("widget_name", $instance["title"]);
		$limit = $instance["limit"];
		$cat = $instance["cat"];
		$orderby = $instance["orderby"];
		$img = $instance["img"];
		$style = "";

		if (!$img) {
			$style = " class=\"nopic\"";
		}

		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<ul" . $style . ">";
		echo dtheme_posts_list($orderby, $limit, $cat, $img);
		echo "</ul>";
		echo $after_widget;
	}

	public function form($instance)
	{
		$defaults = array("title" => "热门文章", "limit" => 6, "orderby" => "comment_count", "img" => "");
		$instance = wp_parse_args((array) $instance, $defaults);
		echo "\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t标题：\r\n\t\t\t\t<input style=\"width:100%;\" id=\"";
		echo $this->get_field_id("title");
		echo "\" name=\"";
		echo $this->get_field_name("title");
		echo "\" type=\"text\" value=\"";
		echo $instance["title"];
		echo "\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t排序：\r\n\t\t\t\t<select style=\"width:100%;\" id=\"";
		echo $this->get_field_id("orderby");
		echo "\" name=\"";
		echo $this->get_field_name("orderby");
		echo "\" style=\"width:100%;\">\r\n\t\t\t\t\t<option value=\"comment_count\" ";
		selected("comment_count", $instance["orderby"]);
		echo ">评论数</option>\r\n\t\t\t\t\t<option value=\"date\" ";
		selected("date", $instance["orderby"]);
		echo ">发布时间</option>\r\n\t\t\t\t\t<option value=\"rand\" ";
		selected("rand", $instance["orderby"]);
		echo ">随机</option>\r\n\t\t\t\t</select>\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t分类限制：\r\n\t\t\t\t<a style=\"font-weight:bold;color:#f60;text-decoration:none;\" href=\"javascript:;\" title=\"格式：1,2 &nbsp;表限制ID为1,2分类的文章&#13;格式：-1,-2 &nbsp;表排除分类ID为1,2的文章&#13;也可直接写1或者-1；注意逗号须是英文的\">？</a>\r\n\t\t\t\t<input style=\"width:100%;\" id=\"";
		echo $this->get_field_id("cat");
		echo "\" name=\"";
		echo $this->get_field_name("cat");
		echo "\" type=\"text\" value=\"";
		echo $instance["cat"];
		echo "\" size=\"24\" />\r\n\t\t\t</label>\r\n\t\t</p>\r\n\t\t<p>\r\n\t\t\t<label>\r\n\t\t\t\t显示数目：\r\n\t\t\t\t<input style=\"width:100%;\" id=\"";
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
