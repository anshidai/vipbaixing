<?php

$pagemenus = _hui("page_menu");
$menus = "";

if ($pagemenus) {
	foreach ($pagemenus as $key => $value ) {
		if ($value) {
			$menus .= "<li><a href=\"" . get_permalink($key) . "\">" . get_post($key)->post_title . "</a></li>";
		}
	}
}

echo "<div class=\"pageside\">\r\n\t<div class=\"pagemenus\">\r\n\t\t<ul class=\"pagemenu\">\r\n\t\t\t";
echo $menus;
echo "\t\t</ul>\r\n\t</div>\r\n</div>";

?>
