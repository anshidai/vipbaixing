<?php

get_header();
echo "\r\n<section class=\"container\">\r\n\t<div class=\"content-wrap\">\r\n\t<div class=\"content\">\r\n\t\t";
_the_ads($name = "ads_tag_01", $class = "asb-tag asb-tag-01");
echo "\t\t";
$pagedtext = "";
if ($paged && (1 < $paged)) {
	$pagedtext = " <small>第" . $paged . "页</small>";
}

echo "<div class=\"pagetitle\"><h1>标签：";
echo single_tag_title();
echo "</h1>" . $pagedtext . "</div>";
get_template_part("excerpt");
echo "\t</div>\r\n\t</div>\r\n\t";
get_sidebar();
echo "</section>\r\n\r\n";
get_footer();

?>
