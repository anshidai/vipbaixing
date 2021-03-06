<?php

class Options_Framework_Interface
{
	static public function optionsframework_tabs()
	{
		$counter = 0;
		$options = &Options_Framework::_optionsframework_options();
		$menu = "";

		foreach ($options as $value ) {
			if ($value["type"] == "heading") {
				$counter++;
				$class = "";
				$class = (!empty($value["id"]) ? $value["id"] : $value["name"]);
				$class = preg_replace("/[^a-zA-Z0-9._\-]/", "", strtolower($class)) . "-tab";
				$menu .= "<a id=\"options-group-" . $counter . "-tab\" class=\"nav-tab " . $class . "\" title=\"" . esc_attr($value["name"]) . "\" href=\"" . esc_attr("#options-group-" . $counter) . "\">" . esc_html($value["name"]) . "</a>";
			}
		}

		return $menu;
	}

	static public function optionsframework_fields()
	{
		global $allowedtags;
		$options_framework = new Options_Framework();
		$option_name = $options_framework->get_option_name();
		$settings = get_option($option_name);
		$options = &Options_Framework::_optionsframework_options();
		$counter = 0;
		$menu = "";

		foreach ($options as $value ) {
			$val = "";
			$select_value = "";
			$output = "";
			if (($value["type"] != "heading") && ($value["type"] != "info")) {
				$value["id"] = preg_replace("/[^a-zA-Z0-9._\-]/", "", strtolower($value["id"]));
				$id = "section-" . $value["id"];
				$class = "section";

				if ($value["type"]) {
					$class .= " section-" . $value["type"];
				}

				if ($value["class"]) {
					$class .= " " . $value["class"];
				}

				$output .= "<div id=\"" . esc_attr($id) . "\" class=\"" . esc_attr($class) . "\">\n";

				if ($value["name"]) {
					$output .= "<h4 class=\"heading\">" . esc_html($value["name"]) . "</h4>\n";
				}

				if ($value["type"] != "editor") {
					$output .= "<div class=\"option\">\n<div class=\"controls\">\n";
				}
				else {
					$output .= "<div class=\"option\">\n<div>\n";
				}
			}

			if ($value["std"]) {
				$val = $value["std"];
			}

			if (($value["type"] != "heading") && ($value["type"] != "info")) {
				if ($settings[$value["id"]]) {
					$val = $settings[$value["id"]];

					if (!is_array($val)) {
						$val = stripslashes($val);
					}
				}
			}

			$explain_value = "";

			if ($value["desc"]) {
				$explain_value = $value["desc"];
			}

			$placeholder = "";

			if ($value["placeholder"]) {
				$placeholder = " placeholder=\"" . esc_attr($value["placeholder"]) . "\"";
			}

			if (has_filter("optionsframework_" . $value["type"])) {
				$output .= apply_filters("optionsframework_" . $value["type"], $option_name, $value, $val);
			}

			switch ($value["type"]) {
			case "text":
				$output .= "<input id=\"" . esc_attr($value["id"]) . "\" class=\"of-input\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "]") . "\" type=\"text\" value=\"" . esc_attr($val) . "\"" . $placeholder . " />";
				break;

			case "password":
				$output .= "<input id=\"" . esc_attr($value["id"]) . "\" class=\"of-input\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "]") . "\" type=\"password\" value=\"" . esc_attr($val) . "\" />";
				break;

			case "textarea":
				$rows = "8";

				if ($value["settings"]["rows"]) {
					$custom_rows = $value["settings"]["rows"];

					if (is_numeric($custom_rows)) {
						$rows = $custom_rows;
					}
				}

				$val = stripslashes($val);
				$output .= "<textarea id=\"" . esc_attr($value["id"]) . "\" class=\"of-input\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "]") . "\" rows=\"" . $rows . "\"" . $placeholder . ">" . esc_textarea($val) . "</textarea>";
				break;

			case "select":
				$output .= "<select class=\"of-input\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "]") . "\" id=\"" . esc_attr($value["id"]) . "\">";

				foreach ($value["options"] as $key => $option ) {
					$output .= "<option" . selected($val, $key, false) . " value=\"" . esc_attr($key) . "\">" . esc_html($option) . "</option>";
				}

				$output .= "</select>";
				break;

			case "radio":
				$name = $option_name . "[" . $value["id"] . "]";

				foreach ($value["options"] as $key => $option ) {
					$id = $option_name . "-" . $value["id"] . "-" . $key;
					$output .= "<input class=\"of-input of-radio\" type=\"radio\" name=\"" . esc_attr($name) . "\" id=\"" . esc_attr($id) . "\" value=\"" . esc_attr($key) . "\" " . checked($val, $key, false) . " /><label for=\"" . esc_attr($id) . "\">" . esc_html($option) . "</label>";
				}

				break;

			case "images":
				$name = $option_name . "[" . $value["id"] . "]";

				foreach ($value["options"] as $key => $option ) {
					$selected = "";
					if (($val != "") && ($val == $key)) {
						$selected = " of-radio-img-selected";
					}

					$output .= "<input type=\"radio\" id=\"" . esc_attr($value["id"] . "_" . $key) . "\" class=\"of-radio-img-radio\" value=\"" . esc_attr($key) . "\" name=\"" . esc_attr($name) . "\" " . checked($val, $key, false) . " />";
					$output .= "<div class=\"of-radio-img-label\">" . esc_html($key) . "</div>";
					$output .= "<img src=\"" . esc_url($option) . "\" alt=\"" . $option . "\" class=\"of-radio-img-img" . $selected . "\" onclick=\"document.getElementById('" . esc_attr($value["id"] . "_" . $key) . "').checked=true;\" />";
				}

				break;

			case "colorradio":
				$name = $option_name . "[" . $value["id"] . "]";

				foreach ($value["options"] as $key => $key ) {
					$selected = "";
					$checked = "";

					if ($val != "") {
						if ($val == $key) {
							$selected = " of-radio-img-selected";
							$checked = " checked=\"checked\"";
						}
					}

					$output .= "<input type=\"radio\" id=\"" . esc_attr($value["id"] . "_" . $key) . "\" class=\"of-radio-img-radio\" value=\"" . esc_attr($key) . "\" name=\"" . esc_attr($name) . "\" " . $checked . " />";
					$output .= "<div class=\"of-radio-img-label\">" . esc_html($key) . "</div>";
					$output .= "<a style=\"background-color:#" . $key . ";\" href=\"javascript:;\" class=\"of-radio-img-img of-radio-color" . $selected . "\" onclick=\"document.getElementById('" . esc_attr($value["id"] . "_" . $key) . "').checked=true;\"></a>";
				}

				break;

			case "checkbox":
				$output .= "<input id=\"" . esc_attr($value["id"]) . "\" class=\"checkbox of-input\" type=\"checkbox\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "]") . "\" " . checked($val, 1, false) . " />";
				$output .= "<label class=\"explain\" for=\"" . esc_attr($value["id"]) . "\">" . wp_kses($explain_value, $allowedtags) . "</label>";
				break;

			case "multicheck":
				foreach ($value["options"] as $key => $option ) {
					$checked = "";
					$label = $option;
					$option = preg_replace("/[^a-zA-Z0-9._\-]/", "", strtolower($key));
					$id = $option_name . "-" . $value["id"] . "-" . $option;
					$name = $option_name . "[" . $value["id"] . "][" . $option . "]";

					if ($val[$option]) {
						$checked = checked($val[$option], 1, false);
					}

					$output .= "<input id=\"" . esc_attr($id) . "\" class=\"checkbox of-input\" type=\"checkbox\" name=\"" . esc_attr($name) . "\" " . $checked . " /><label for=\"" . esc_attr($id) . "\">" . esc_html($label) . "</label>";
				}

				break;

			case "color":
				$default_color = "";

				if ($value["std"]) {
					if ($val != $value["std"]) {
						$default_color = " data-default-color=\"" . $value["std"] . "\" ";
					}
				}

				$output .= "<input name=\"" . esc_attr($option_name . "[" . $value["id"] . "]") . "\" id=\"" . esc_attr($value["id"]) . "\" class=\"of-color\"  type=\"text\" value=\"" . esc_attr($val) . "\"" . $default_color . " />";
				break;

			case "upload":
				$output .= Options_Framework_Media_Uploader::optionsframework_uploader($value["id"], $val, NULL);
				break;

			case "typography":
				unset($font_size);
				unset($font_style);
				unset($font_face);
				unset($font_color);
				$typography_defaults = array("size" => "", "face" => "", "style" => "", "color" => "");
				$typography_stored = wp_parse_args($val, $typography_defaults);
				$typography_options = array("sizes" => of_recognized_font_sizes(), "faces" => of_recognized_font_faces(), "styles" => of_recognized_font_styles(), "color" => true);

				if ($value["options"]) {
					$typography_options = wp_parse_args($value["options"], $typography_options);
				}

				if ($typography_options["sizes"]) {
					$font_size = "<select class=\"of-typography of-typography-size\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "][size]") . "\" id=\"" . esc_attr($value["id"] . "_size") . "\">";
					$sizes = $typography_options["sizes"];

					foreach ($sizes as $i ) {
						$size = $i . "px";
						$font_size .= "<option value=\"" . esc_attr($size) . "\" " . selected($typography_stored["size"], $size, false) . ">" . esc_html($size) . "</option>";
					}

					$font_size .= "</select>";
				}

				if ($typography_options["faces"]) {
					$font_face = "<select class=\"of-typography of-typography-face\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "][face]") . "\" id=\"" . esc_attr($value["id"] . "_face") . "\">";
					$faces = $typography_options["faces"];

					foreach ($faces as $key => $face ) {
						$font_face .= "<option value=\"" . esc_attr($key) . "\" " . selected($typography_stored["face"], $key, false) . ">" . esc_html($face) . "</option>";
					}

					$font_face .= "</select>";
				}

				if ($typography_options["styles"]) {
					$font_style = "<select class=\"of-typography of-typography-style\" name=\"" . $option_name . "[" . $value["id"] . "][style]\" id=\"" . $value["id"] . "_style\">";
					$styles = $typography_options["styles"];

					foreach ($styles as $key => $style ) {
						$font_style .= "<option value=\"" . esc_attr($key) . "\" " . selected($typography_stored["style"], $key, false) . ">" . $style . "</option>";
					}

					$font_style .= "</select>";
				}

				if ($typography_options["color"]) {
					$default_color = "";

					if ($value["std"]["color"]) {
						if ($val != $value["std"]["color"]) {
							$default_color = " data-default-color=\"" . $value["std"]["color"] . "\" ";
						}
					}

					$font_color = "<input name=\"" . esc_attr($option_name . "[" . $value["id"] . "][color]") . "\" id=\"" . esc_attr($value["id"] . "_color") . "\" class=\"of-color of-typography-color  type=\"text\" value=\"" . esc_attr($typography_stored["color"]) . "\"" . $default_color . " />";
				}

				$typography_fields = compact("font_size", "font_face", "font_style", "font_color");
				$typography_fields = apply_filters("of_typography_fields", $typography_fields, $typography_stored, $option_name, $value);
				$output .= implode("", $typography_fields);
				break;

			case "background":
				$background = $val;
				$default_color = "";

				if ($value["std"]["color"]) {
					if ($val != $value["std"]["color"]) {
						$default_color = " data-default-color=\"" . $value["std"]["color"] . "\" ";
					}
				}

				$output .= "<input name=\"" . esc_attr($option_name . "[" . $value["id"] . "][color]") . "\" id=\"" . esc_attr($value["id"] . "_color") . "\" class=\"of-color of-background-color\"  type=\"text\" value=\"" . esc_attr($background["color"]) . "\"" . $default_color . " />";

				if (!$background["image"]) {
					$background["image"] = "";
				}

				$output .= Options_Framework_Media_Uploader::optionsframework_uploader($value["id"], $background["image"], NULL, esc_attr($option_name . "[" . $value["id"] . "][image]"));
				$class = "of-background-properties";

				if ("" == $background["image"]) {
					$class .= " hide";
				}

				$output .= "<div class=\"" . esc_attr($class) . "\">";
				$output .= "<select class=\"of-background of-background-repeat\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "][repeat]") . "\" id=\"" . esc_attr($value["id"] . "_repeat") . "\">";
				$repeats = of_recognized_background_repeat();

				foreach ($repeats as $key => $repeat ) {
					$output .= "<option value=\"" . esc_attr($key) . "\" " . selected($background["repeat"], $key, false) . ">" . esc_html($repeat) . "</option>";
				}

				$output .= "</select>";
				$output .= "<select class=\"of-background of-background-position\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "][position]") . "\" id=\"" . esc_attr($value["id"] . "_position") . "\">";
				$positions = of_recognized_background_position();

				foreach ($positions as $key => $position ) {
					$output .= "<option value=\"" . esc_attr($key) . "\" " . selected($background["position"], $key, false) . ">" . esc_html($position) . "</option>";
				}

				$output .= "</select>";
				$output .= "<select class=\"of-background of-background-attachment\" name=\"" . esc_attr($option_name . "[" . $value["id"] . "][attachment]") . "\" id=\"" . esc_attr($value["id"] . "_attachment") . "\">";
				$attachments = of_recognized_background_attachment();

				foreach ($attachments as $key => $attachment ) {
					$output .= "<option value=\"" . esc_attr($key) . "\" " . selected($background["attachment"], $key, false) . ">" . esc_html($attachment) . "</option>";
				}

				$output .= "</select>";
				$output .= "</div>";
				break;

			case "editor":
				$output .= "<div class=\"explain\">" . wp_kses($explain_value, $allowedtags) . "</div>\n";
				echo $output;
				$textarea_name = esc_attr($option_name . "[" . $value["id"] . "]");
				$default_editor_settings = array(
					"textarea_name" => $textarea_name,
					"media_buttons" => false,
					"tinymce"       => array("plugins" => "wordpress")
					);
				$editor_settings = array();

				if ($value["settings"]) {
					$editor_settings = $value["settings"];
				}

				$editor_settings = array_merge($default_editor_settings, $editor_settings);
				wp_editor($val, $value["id"], $editor_settings);
				$output = "";
				break;

			case "info":
				$id = "";
				$class = "section";

				if ($value["id"]) {
					$id = "id=\"" . esc_attr($value["id"]) . "\" ";
				}

				if ($value["type"]) {
					$class .= " section-" . $value["type"];
				}

				if ($value["class"]) {
					$class .= " " . $value["class"];
				}

				$output .= "<div " . $id . "class=\"" . esc_attr($class) . "\">\n";

				if ($value["name"]) {
					$output .= "<h4 class=\"heading\">" . esc_html($value["name"]) . "</h4>\n";
				}

				if ($value["desc"]) {
					$output .= $value["desc"] . "\n";
				}

				$output .= "</div>\n";
				break;

			case "heading":
				$counter++;

				if (2 <= $counter) {
					$output .= "</div>\n";
				}

				$class = "";
				$class = (!$value["id"] ? $value["id"] : $value["name"]);
				$class = preg_replace("/[^a-zA-Z0-9._\-]/", "", strtolower($class));
				$output .= "<div id=\"options-group-" . $counter . "\" class=\"group " . $class . "\">";
				$output .= "<h3>" . esc_html($value["name"]) . "</h3>\n";
				break;
			}

			if (($value["type"] != "heading") && ($value["type"] != "info")) {
				$output .= "</div>";
				if (($value["type"] != "checkbox") && ($value["type"] != "editor")) {
					$output .= "<div class=\"explain\">" . wp_kses($explain_value, $allowedtags) . "</div>\n";
				}

				$output .= "</div></div>\n";
			}

			echo $output;
		}

		if (Options_Framework_Interface::optionsframework_tabs() != "") {
			echo "</div>";
		}
	}
}


?>
