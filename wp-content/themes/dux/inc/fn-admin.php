<?php

function optionsframework_custom_scripts()
{
	echo "\t<script type=\"text/javascript\">\r\n\tjQuery(document).ready(function() {\r\n\r\n\t\tjQuery('#example_showhidden').click(function() {\r\n\t  \t\tjQuery('#section-example_text_hidden').fadeToggle(400);\r\n\t\t});\r\n\r\n\t\tif (jQuery('#example_showhidden:checked').val() !== undefined) {\r\n\t\t\tjQuery('#section-example_text_hidden').show();\r\n\t\t}\r\n\r\n\t});\r\n\t</script>\r\n\r\n\t";
}

function _admin_comment_ctrlenter()
{
	echo "<script type=\"text/javascript\">\r\n        jQuery(document).ready(function($){\r\n            $(\"textarea\").keypress(function(e){\r\n                if(e.ctrlKey&&e.which==13||e.which==10){\r\n                    $(\"#replybtn\").click();\r\n                }\r\n            });\r\n        });\r\n    </script>";
}

function _add_editor_buttons($buttons)
{
	$buttons[] = "fontselect";
	$buttons[] = "fontsizeselect";
	$buttons[] = "cleanup";
	$buttons[] = "styleselect";
	$buttons[] = "del";
	$buttons[] = "sub";
	$buttons[] = "sup";
	$buttons[] = "copy";
	$buttons[] = "paste";
	$buttons[] = "cut";
	$buttons[] = "image";
	$buttons[] = "anchor";
	$buttons[] = "backcolor";
	$buttons[] = "wp_page";
	$buttons[] = "charmap";
	return $buttons;
}

function remove_open_sans()
{
	wp_deregister_style("open-sans");
	wp_register_style("open-sans", false);
	wp_enqueue_style("open-sans", "");
}

function _postmeta_from()
{
	global $post;
	global $postmeta_from;

	foreach ($postmeta_from as $meta_box ) {
		$meta_box_value = get_post_meta($post->ID, $meta_box["name"] . "_value", true);

		if ($meta_box_value == "") {
			$meta_box_value = $meta_box["std"];
		}

		echo "<p>" . $meta_box["title"] . "</p>";
		echo "<p><input type=\"text\" style=\"width:98%\" value=\"" . $meta_box_value . "\" name=\"" . $meta_box["name"] . "_value\"></p>";
	}

	echo "<input type=\"hidden\" name=\"post_newmetaboxes_noncename\" id=\"post_newmetaboxes_noncename\" value=\"" . wp_create_nonce(plugin_basename(__FILE__)) . "\" />";
}

function _postmeta_from_create()
{
	global $theme_name;

	if (function_exists("add_meta_box")) {
		add_meta_box("new-meta-boxes", __("来源", "haoui"), "_postmeta_from", "post", "normal", "high");
	}
}

function _postmeta_from_save($post_id)
{
	global $postmeta_from;

	if (!wp_verify_nonce($_POST["post_newmetaboxes_noncename"], plugin_basename(__FILE__))) {
		return NULL;
	}

	if (!current_user_can("edit_posts", $post_id)) {
		return NULL;
	}

	foreach ($postmeta_from as $meta_box ) {
		$data = $_POST[$meta_box["name"] . "_value"];

		if (get_post_meta($post_id, $meta_box["name"] . "_value") == "") {
			add_post_meta($post_id, $meta_box["name"] . "_value", $data, true);
		}
		else if ($data != get_post_meta($post_id, $meta_box["name"] . "_value", true)) {
			update_post_meta($post_id, $meta_box["name"] . "_value", $data);
		}
		else if ($data == "") {
			delete_post_meta($post_id, $meta_box["name"] . "_value", get_post_meta($post_id, $meta_box["name"] . "_value", true));
		}
	}
}

function _postmeta_keywords_description()
{
	global $post;
	global $postmeta_keywords_description;

	foreach ($postmeta_keywords_description as $meta_box ) {
		$meta_box_value = get_post_meta($post->ID, $meta_box["name"], true);

		if ($meta_box_value == "") {
			$meta_box_value = $meta_box["std"];
		}

		echo "<p>" . $meta_box["title"] . "</p>";

		if ($meta_box["name"] == "keywords") {
			echo "<p><input type=\"text\" style=\"width:98%\" value=\"" . $meta_box_value . "\" name=\"" . $meta_box["name"] . "\"></p>";
		}
		else {
			echo "<p><textarea style=\"width:98%\" name=\"" . $meta_box["name"] . "\">" . $meta_box_value . "</textarea></p>";
		}
	}

	echo "<input type=\"hidden\" name=\"post_newmetaboxes_noncename\" id=\"post_newmetaboxes_noncename\" value=\"" . wp_create_nonce(plugin_basename(__FILE__)) . "\" />";
}

function _postmeta_keywords_description_create()
{
	global $theme_name;

	if (function_exists("add_meta_box")) {
		add_meta_box("postmeta_keywords_description_boxes", __("自定义关键字和描述", "haoui"), "_postmeta_keywords_description", "post", "normal", "high");
	}
}

function _postmeta_keywords_description_save($post_id)
{
	global $postmeta_keywords_description;

	if (!wp_verify_nonce($_POST["post_newmetaboxes_noncename"], plugin_basename(__FILE__))) {
		return NULL;
	}

	if (!current_user_can("edit_posts", $post_id)) {
		return NULL;
	}

	foreach ($postmeta_keywords_description as $meta_box ) {
		$data = $_POST[$meta_box["name"]];

		if (get_post_meta($post_id, $meta_box["name"]) == "") {
			add_post_meta($post_id, $meta_box["name"], $data, true);
		}
		else if ($data != get_post_meta($post_id, $meta_box["name"], true)) {
			update_post_meta($post_id, $meta_box["name"], $data);
		}
		else if ($data == "") {
			delete_post_meta($post_id, $meta_box["name"], get_post_meta($post_id, $meta_box["name"], true));
		}
	}
}

function _postmeta_link()
{
	global $post;
	global $postmeta_link;

	foreach ($postmeta_link as $meta_box ) {
		$meta_box_value = get_post_meta($post->ID, $meta_box["name"], true);

		if ($meta_box_value == "") {
			$meta_box_value = $meta_box["std"];
		}

		echo "<p>" . $meta_box["title"] . "</p>";
		echo "<p><input type=\"text\" style=\"width:98%\" value=\"" . $meta_box_value . "\" name=\"" . $meta_box["name"] . "\"></p>";
	}

	echo "<input type=\"hidden\" name=\"post_newmetaboxes_noncename\" id=\"post_newmetaboxes_noncename\" value=\"" . wp_create_nonce(plugin_basename(__FILE__)) . "\" />";
}

function _postmeta_link_create()
{
	global $theme_name;

	if (function_exists("add_meta_box")) {
		add_meta_box("postmeta_link_boxes", __("直达链接", "haoui"), "_postmeta_link", "post", "normal", "high");
	}
}

function _postmeta_link_save($post_id)
{
	global $postmeta_link;

	if (!wp_verify_nonce($_POST["post_newmetaboxes_noncename"], plugin_basename(__FILE__))) {
		return NULL;
	}

	if (!current_user_can("edit_posts", $post_id)) {
		return NULL;
	}

	foreach ($postmeta_link as $meta_box ) {
		$data = $_POST[$meta_box["name"]];

		if (get_post_meta($post_id, $meta_box["name"]) == "") {
			add_post_meta($post_id, $meta_box["name"], $data, true);
		}
		else if ($data != get_post_meta($post_id, $meta_box["name"], true)) {
			update_post_meta($post_id, $meta_box["name"], $data);
		}
		else if ($data == "") {
			delete_post_meta($post_id, $meta_box["name"], get_post_meta($post_id, $meta_box["name"], true));
		}
	}
}

define("OPTIONS_FRAMEWORK_DIRECTORY", get_template_directory_uri() . "/settings/");
require_once (get_stylesheet_directory() . "/settings/options-framework.php");
require_once (get_stylesheet_directory() . "/options.php");
add_action("optionsframework_custom_scripts", "optionsframework_custom_scripts");
add_editor_style(get_locale_stylesheet_uri() . "/css/editor-style.css");
add_action("admin_footer", "_admin_comment_ctrlenter");
add_filter("mce_buttons_2", "_add_editor_buttons");
$postmeta_from = array(
	array("name" => "fromname", "std" => "", "title" => __("来源名", "haoui") . "："),
	array("name" => "fromurl", "std" => "", "title" => __("来源网址", "haoui") . "：")
	);

if (_hui("post_from_s")) {
	add_action("admin_menu", "_postmeta_from_create");
	add_action("save_post", "_postmeta_from_save");
}

$postmeta_keywords_description = array(
	array("name" => "keywords", "std" => "", "title" => __("关键字", "haoui") . "："),
	array("name" => "description", "std" => "", "title" => __("描述", "haoui") . "：")
	);

if (_hui("post_keywords_description_s")) {
	add_action("admin_menu", "_postmeta_keywords_description_create");
	add_action("save_post", "_postmeta_keywords_description_save");
}

$postmeta_link = array(
	array("name" => "link", "std" => "")
	);
if (_hui("post_link_excerpt_s") || _hui("post_link_single_s")) {
	add_action("admin_menu", "_postmeta_link_create");
	add_action("save_post", "_postmeta_link_save");
}

?>
