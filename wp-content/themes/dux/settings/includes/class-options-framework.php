<?php

class Options_Framework
{
	const VERSION = "1.9.0";

	public function get_option_name()
	{
		$name = "";

		if (function_exists("optionsframework_option_name")) {
			$name = optionsframework_option_name();
		}

		if ("" == $name) {
			$name = get_option("stylesheet");
			$name = preg_replace("/\W/", "_", strtolower($name));
		}

		return $name;
	}

	static public function _optionsframework_options()
	{
		static $options;

		if (!$options) {
			$location = apply_filters("options_framework_location", array("options.php"));

			if ($optionsfile = locate_template($location)) {
				$maybe_options = require_once ($optionsfile);

				if (is_array($maybe_options)) {
					$options = $maybe_options;
				}
				else if (function_exists("optionsframework_options")) {
					$options = optionsframework_options();
				}
			}

			$options = apply_filters("of_options", $options);
		}

		return $options;
	}
}


?>
