<?php

function readers_wall($outer = "1", $timer = "3", $limit = "100")
{
	global $wpdb;
	$counts = $wpdb->get_results("SELECT count(comment_author) AS cnt, comment_author, comment_author_url, comment_author_email FROM $wpdb->comments WHERE comment_date > date_sub( now(), interval $timer month ) AND user_id!='1' AND comment_author!=$outer AND comment_approved='1' AND comment_type='' GROUP BY comment_author ORDER BY cnt DESC LIMIT $limit");
	$i = 0;

	foreach ($counts as $count ) {
		$i++;
		$c_url = $count->comment_author_url;

		if (!$c_url) {
			$c_url = "javascript:;";
		}

		$tt = $i;

		if ($i == 1) {
			$tt = "金牌读者";
		}
		else if ($i == 2) {
			$tt = "银牌读者";
		}
		else if ($i == 3) {
			$tt = "铜牌读者";
		}
		else {
			$tt = "第" . $i . "名";
		}

		if ($i < 4) {
			$type .= "<a class=\"item-top item-" . $i . "\" target=\"_blank\" href=\"" . $c_url . "\"><h4>【" . $tt . "】<small>评论：" . $count->cnt . "</small></h4>" . str_replace(" src=", " data-src=", get_avatar($count->comment_author_email, $size = "36", AVATAR_DEFAULT)) . "<strong>" . $count->comment_author . "</strong>" . $c_url . "</a>";
		}
		else {
			$type .= "<a target=\"_blank\" href=\"" . $c_url . "\" title=\"【" . $tt . "】评论：" . $count->cnt . "\">" . str_replace(" src=", " data-src=", get_avatar($count->comment_author_email, $size = "36", AVATAR_DEFAULT)) . $count->comment_author . "</a>";
		}
	}

	echo $type;
}

echo "\r\n<div class=\"container container-page\">\r\n\t";
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

echo "\r\n\t\t<div class=\"readers\">\r\n\t\t\t";
echo "\t\t\t";
readers_wall(1, _hui("readwall_limit_time"), _hui("readwall_limit_number"));
echo "\t\t</div>\r\n\r\n\t\t";
comments_template("", true);
echo "\t</div>\r\n</div>";

?>
