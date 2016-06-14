<?php

echo "<div class=\"container container-page\">\r\n\t";
_moloader("mo_pagemenu", false);
echo "\t<div class=\"content\">\r\n\t\t";

while (have_posts()) {
	the_post();
	echo "\t\t<header class=\"article-header\">\r\n\t\t\t<h1 class=\"article-title\"><a href=\"";
	the_permalink();
	echo "\">";
	the_title();
	echo "</a></h1>\r\n\t\t</header>\r\n\t\t<article class=\"article-content\">\r\n\t\t\t";
	the_content();
	echo "\t\t</article>\r\n\t\t";
}

echo "\r\n\t\t<ul class=\"plinks\">\r\n\t\t\t";
$links_cat = _hui("page_links_cat");
$links = array();

if ($links_cat) {
	foreach ($links_cat as $key => $value ) {
		if ($value) {
			$links[] = $key;
		}
	}
}

$links = implode(",", $links);

if (!$links) {
	wp_list_bookmarks(array("category" => $links));
}

echo "\t\t</ul>\r\n\r\n\t\t";
comments_template("", true);
echo "\t</div>\r\n</div>";

?>
