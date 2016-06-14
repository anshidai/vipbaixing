<?php

function mo_paging()
{
	$p = 3;

	if (is_singular()) {
		return NULL;
	}

	global $wp_query;
	global $paged;
	$max_page = $wp_query->max_num_pages;

	if ($max_page == 1) {
		return NULL;
	}

	echo "<div class=\"pagination\"><ul>";

	if (empty($paged)) {
		$paged = 1;
	}

	echo "<li class=\"prev-page\">";
	previous_posts_link("上一页");
	echo "</li>";

	if (($p + 1) < $paged) {
		_paging_link(1, "<li>第一页</li>");
	}

	if (($p + 2) < $paged) {
		echo "<li><span>···</span></li>";
	}

	for ($i = $paged - $p; $i <= $paged + $p; $i++) {
		if ((0 < $i) && ($i <= $max_page)) {
			$i == $paged ? print("<li class=\"active\"><span>$i</span></li>") : _paging_link($i);
		}
	}

	if ($paged < ($max_page - $p - 1)) {
		echo "<li><span> ... </span></li>";
	}

	echo "<li class=\"next-page\">";
	next_posts_link("下一页");
	echo "</li>";
	echo "<li><span>共 " . $max_page . " 页</span></li>";
	echo "</ul></div>";
}

function _paging_link($i, $title = false)
{
	if ($title == "") {
		$title = "第 $i 页";
	}

	echo "<li><a href='";
	echo esc_html(get_pagenum_link($i));
	echo "'>$i</a></li>";
}


?>
