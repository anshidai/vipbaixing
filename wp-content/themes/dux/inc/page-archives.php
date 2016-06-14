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

echo "\r\n\t\t<article class=\"archives\">\r\n            ";
$previous_year = $year = 0;
$previous_month = $month = 0;
$ul_open = false;
$myposts = get_posts("numberposts=-1&orderby=post_date&order=DESC");

foreach ($myposts as $post ) {
	setup_postdata($post);
	$year = mysql2date("Y", $post->post_date);
	$month = mysql2date("n", $post->post_date);
	$day = mysql2date("j", $post->post_date);
	if (($year != $previous_year) || ($month != $previous_month)) {
		if ($ul_open == true) {
			echo "</ul></div>";
		}

		echo "<div class=\"item\"><h3>";
		echo the_time("F Y");
		echo "</h3>";
		echo "<ul class=\"archives-list\">";
		$ul_open = true;
	}

	$previous_year = $year;
	$previous_month = $month;
	echo "                <li>\r\n                    <time>";
	the_time("j");
	echo "日</time>\r\n                    <a href=\"";
	the_permalink();
	echo "\">";
	the_title();
	echo " </a>\r\n                    <span class=\"text-muted\">";
	comments_number("", "1评论", "%评论");
	echo "</span>\r\n                </li>\r\n            ";
}

echo "            </ul>\r\n        </div>\r\n        </article>\r\n\r\n\t\t";
echo "\t</div>\r\n</div>\r\n";

?>
