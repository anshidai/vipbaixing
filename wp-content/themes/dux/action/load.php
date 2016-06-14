<?php

function is_disable_username($name)
{
	global $disable_reg_keywords;
	if (!$disable_reg_keywords || !$name) {
		return false;
	}

	foreach ($disable_reg_keywords as $value ) {
		if (!$value && is_in_str(strtolower($name), strtolower($value))) {
			return true;
		}
	}

	return false;
}

function is_in_str($haystack, $needle = true)
{
	$haystack = "-_-!" . $haystack;
	return (bool) strpos($haystack, $needle);
}

function get_millisecond()
{
	list($s1, $s2) = explode(" ", microtime());
	return (double) sprintf("%.0f", (floatval($s1) + floatval($s2)) * 1000);
}

function sstrlen($str, $charset = true)
{
	$n = 0;
	$p = 0;
	$c = "";
	$len = strlen($str);

	if ($charset == "utf-8") {
		for ($i = 0; $i < $len; $i++) {
			$c = ord($str[$i]);

			if (252 < $c) {
				$p = 5;
			}
			else if (248 < $c) {
				$p = 4;
			}
			else if (240 < $c) {
				$p = 3;
			}
			else if (224 < $c) {
				$p = 2;
			}
			else if (192 < $c) {
				$p = 1;
			}
			else {
				$p = 0;
			}

			$i += $p;
			$n++;
		}
	}
	else {
		for ($i = 0; $i < $len; $i++) {
			$c = ord($str[$i]);

			if (127 < $c) {
				$p = 1;
			}
			else {
				$p = 0;
			}

			$i += $p;
			$n++;
		}
	}

	return $n;
}

require (dirname(__FILE__) . "/../../../../wp-load.php");
$cuid = get_current_user_id();

?>
