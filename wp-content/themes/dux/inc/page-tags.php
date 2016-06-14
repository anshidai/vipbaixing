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

echo "\r\n\t\t<div class=\"tag-clouds\">\r\n\t\t\t";
$tags_list = get_tags("orderby=count&order=DESC");

if ($tags_list) {
	foreach ($tags_list as $tag ) {
		echo "<a href=\"" . get_tag_link($tag) . "\">" . $tag->name . "<small>(" . $tag->count . ")</small></a>";
	}
}

echo "\t\t</div>\r\n\t</div>\r\n</div>";

?>
