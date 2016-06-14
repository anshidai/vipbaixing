<?php

class Options_Framework_Admin
{
	/**
     * Page hook for the options screen
     *
     * @since 1.7.0
     * @type string
     */
	protected $options_screen;

	public function init()
	{
		$options = &Options_Framework::_optionsframework_options();

		if ($options) {
			add_action("admin_menu", array($this, "add_custom_options_page"));
			add_action("admin_enqueue_scripts", array($this, "enqueue_admin_styles"));
			add_action("admin_enqueue_scripts", array($this, "enqueue_admin_scripts"));
			add_action("admin_init", array($this, "settings_init"));
			add_action("wp_before_admin_bar_render", array($this, "optionsframework_admin_bar"));
		}
	}

	public function settings_init()
	{
		$options_framework = new Options_Framework();
		$name = $options_framework->get_option_name();
		register_setting("optionsframework", $name, array($this, "validate_options"));
		add_action("optionsframework_after_validate", array($this, "save_options_notice"));
	}

	static public function menu_settings()
	{
		$menu = array("mode" => "submenu", "page_title" => __("DUX 主题设置", "haoui"), "menu_title" => __("DUX 主题设置", "haoui"), "capability" => "edit_theme_options", "menu_slug" => "options-framework", "parent_slug" => "themes.php", "icon_url" => "dashicons-admin-generic", "position" => "61");
		return apply_filters("optionsframework_menu", $menu);
	}

	public function add_custom_options_page()
	{
		$menu = $this->menu_settings();
		$this->options_screen = add_theme_page($menu["page_title"], $menu["menu_title"], $menu["capability"], $menu["menu_slug"], array($this, "options_page"));
	}

	public function enqueue_admin_styles($hook)
	{
		if ($this->options_screen != $hook) {
			return NULL;
		}

		wp_enqueue_style("optionsframework", OPTIONS_FRAMEWORK_DIRECTORY . "css/optionsframework.css", array(), Options_Framework::VERSION);
		wp_enqueue_style("wp-color-picker");
	}

	public function enqueue_admin_scripts($hook)
	{
		if ($this->options_screen != $hook) {
			return NULL;
		}

		wp_enqueue_script("options-custom", OPTIONS_FRAMEWORK_DIRECTORY . "js/options-custom.js", array("jquery", "wp-color-picker"), Options_Framework::VERSION);
		add_action("admin_head", array($this, "of_admin_head"));
	}

	public function of_admin_head()
	{
		do_action("optionsframework_custom_scripts");
	}

	public function options_page()
	{
		echo "\n\t\t<div id=\"optionsframework-wrap\" class=\"wrap\">\n\n\t\t";
		$menu = $this->menu_settings();
		echo "\t\t<h2>";
		echo esc_html($menu["page_title"]);
		echo "</h2>\n\n\t    <h2 class=\"nav-tab-wrapper\">\n\t        ";
		echo Options_Framework_Interface::optionsframework_tabs();
		echo "\t    </h2>\n\n\t    ";
		settings_errors("options-framework");
		echo "\n\t    <div id=\"optionsframework-metabox\" class=\"metabox-holder\">\n\t\t    <div id=\"optionsframework\" class=\"postbox\">\n\t\t\t\t<form action=\"options.php\" method=\"post\">\n\t\t\t\t";
		settings_fields("optionsframework");
		echo "\t\t\t\t";
		Options_Framework_Interface::optionsframework_fields();
		echo "\t\t\t\t<div id=\"optionsframework-submit\">\n\t\t\t\t\t<input type=\"submit\" class=\"button-primary\" name=\"update\" value=\"";
		echo __("保存设置", "haoui");
		echo "\" />\n\t\t\t\t\t<input type=\"submit\" class=\"reset-button button-secondary\" name=\"reset\" value=\"";
		echo __("重置全部设置", "haoui");
		echo "\" onclick=\"return confirm( '";
		print(esc_js(__("Click OK to reset. Any theme settings will be lost!", "textdomain")));
		echo "' );\" />\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t</div>\n\t\t\t\t</form>\n\t\t\t</div> <!-- / #container -->\n\t\t</div>\n\t\t";
		do_action("optionsframework_after");
		echo "\t\t</div> <!-- / .wrap -->\n\n\t";
	}

	public function validate_options($input)
	{
		if ($_POST["reset"]) {
			add_settings_error("options-framework", "restore_defaults", __("设置已重置！", "haoui"), "updated fade");
			return $this->get_default_values();
		}

		$clean = array();
		$options = &Options_Framework::_optionsframework_options();

		foreach ($options as $option ) {
			if (!$option["id"]) {
				continue;
			}

			if (!$option["type"]) {
				continue;
			}

			$id = preg_replace("/[^a-zA-Z0-9._\-]/", "", strtolower($option["id"]));
			if (("checkbox" == $option["type"]) && !$input[$id]) {
				$input[$id] = false;
			}

			if (("multicheck" == $option["type"]) && !$input[$id]) {
				foreach ($option["options"] as $key => $value ) {
					$input[$id][$key] = false;
				}
			}

			if (has_filter("of_sanitize_" . $option["type"])) {
				$clean[$id] = apply_filters("of_sanitize_" . $option["type"], $input[$id], $option);
			}
		}

		do_action("optionsframework_after_validate", $clean);
		return $clean;
	}

	public function save_options_notice()
	{
		add_settings_error("options-framework", "save_options", __("设置保存成功！", "haoui"), "updated fade");
	}

	public function get_default_values()
	{
		$output = array();
		$config = &Options_Framework::_optionsframework_options();

		foreach ((array) $config as $option ) {
			if (!$option["id"]) {
				continue;
			}

			if (!$option["std"]) {
				continue;
			}

			if (!$option["type"]) {
				continue;
			}

			if (has_filter("of_sanitize_" . $option["type"])) {
				$output[$option["id"]] = apply_filters("of_sanitize_" . $option["type"], $option["std"], $option);
			}
		}

		return $output;
	}

	public function optionsframework_admin_bar()
	{
		$menu = $this->menu_settings();
		global $wp_admin_bar;

		if ("menu" == $menu["mode"]) {
			$href = admin_url("admin.php?page=" . $menu["menu_slug"]);
		}
		else {
			$href = admin_url("themes.php?page=" . $menu["menu_slug"]);
		}

		$args = array("parent" => "appearance", "id" => "of_theme_options", "title" => $menu["menu_title"], "href" => $href);
		$wp_admin_bar->add_menu(apply_filters("optionsframework_admin_bar", $args));
	}
}


?>
