<?php

class SimpleURLs
{
	public function __construct()
	{
		add_action("init", array($this, "register_post_type"));
		add_action("manage_posts_custom_column", array($this, "columns_data"));
		add_filter("manage_edit-surl_columns", array($this, "columns_filter"));
		add_action("admin_menu", array($this, "add_meta_box"));
		add_action("save_post", array($this, "meta_box_save"), 1, 2);
		add_action("template_redirect", array($this, "count_and_redirect"));
	}

	public function SimpleURLs()
	{
		$this->__construct();
	}

	public function register_post_type()
	{
		register_post_type("surl", array(
	"labels"        => array("name" => __("Simple URLs"), "singular_name" => __("URL"), "add_new" => __("Add New"), "add_new_item" => __("Add New URL"), "edit" => __("Edit"), "edit_item" => __("Edit URL"), "new_item" => __("New URL"), "view" => __("View URL"), "view_item" => __("View URL"), "search_items" => __("Search URL"), "not_found" => __("No URLs found"), "not_found_in_trash" => __("No URLs found in Trash")),
	"public"        => true,
	"query_var"     => true,
	"menu_position" => 20,
	"supports"      => array("title"),
	"rewrite"       => array("slug" => "go", "with_front" => false)
	));
	}

	public function columns_filter($columns)
	{
		$columns = array("cb" => "<input type=\"checkbox\" />", "title" => __("Title"), "url" => __("Redirect to"), "permalink" => __("Permalink"), "clicks" => __("Clicks"));
		return $columns;
	}

	public function columns_data($column)
	{
		global $post;
		$url = get_post_meta($post->ID, "_surl_redirect", true);
		$count = get_post_meta($post->ID, "_surl_count", true);

		if ($column == "url") {
			echo make_clickable(esc_url($url ? $url : ""));
		}
		else if ($column == "permalink") {
			echo make_clickable(get_permalink());
		}
		else if ($column == "clicks") {
			echo esc_html($count ? $count : 0);
		}
	}

	public function add_meta_box()
	{
		add_meta_box("surl", __("URL Information", "surl"), array(&$this, "meta_box"), "surl", "normal", "high");
	}

	public function meta_box()
	{
		global $post;
		printf("<input type=\"hidden\" name=\"_surl_nonce\" value=\"%s\" />", wp_create_nonce(plugin_basename(__FILE__)));
		printf("<p><label for=\"%s\">%s</label></p>", "_surl_redirect", __("Redirect URI", "surl"));
		printf("<p><input style=\"%s\" type=\"text\" name=\"%s\" id=\"%s\" value=\"%s\" /></p>", "width: 99%;", "_surl_redirect", "_surl_redirect", esc_attr(get_post_meta($post->ID, "_surl_redirect", true)));
		$count = ($post->ID ? get_post_meta($post->ID, "_surl_count", true) : 0);
		printf("<p>This URL has been accessed <b>%d</b> times.", esc_attr($count));
	}

	public function meta_box_save($post_id, $post)
	{
		$key = "_surl_redirect";
		if (!$_POST["_surl_nonce"] || !wp_verify_nonce($_POST["_surl_nonce"], plugin_basename(__FILE__))) {
			return NULL;
		}

		if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
			return NULL;
		}

		if (defined("DOING_AJAX") && DOING_AJAX) {
			return NULL;
		}

		if (defined("DOING_CRON") && DOING_CRON) {
			return NULL;
		}

		if (!current_user_can("edit_posts") || ($post->post_type != "surl")) {
			return NULL;
		}

		$value = ($_POST[$key] ? $_POST[$key] : "");

		if ($value) {
			update_post_meta($post->ID, $key, $value);
		}
		else {
			delete_post_meta($post->ID, $key);
		}
	}

	public function count_and_redirect()
	{
		if (!is_singular("surl")) {
			return NULL;
		}

		global $wp_query;
		$count = ($wp_query->post->ID ? get_post_meta($wp_query->post->ID, "_surl_count", true) : 0);
		update_post_meta($wp_query->post->ID, "_surl_count", $count + 1);
		$redirect = ($wp_query->post->ID ? get_post_meta($wp_query->post->ID, "_surl_redirect", true) : "");

		if (!$redirect) {
			wp_redirect(esc_url_raw($redirect), 301);
			exit();
		}
		else {
			wp_redirect(home_url(), 302);
			exit();
		}
	}
}

$SimpleURLs = new SimpleURLs();

?>
