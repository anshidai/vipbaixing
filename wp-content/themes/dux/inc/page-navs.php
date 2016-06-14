<?php

echo "\r\n<div class=\"pageheader\">\r\n\t<div class=\"container\">\r\n\t\t<div class=\"share bdsharebuttonbox\">\r\n\t\t\t";
_moloader("mo_share", false);
mo_share("renren");
echo "\t\t</div>\r\n\t\t<h1>";
the_title();
echo "</h1>\r\n\t\t<div class=\"note\">";
echo _hui("navpage_desc") ? _hui("navpage_desc") : "这里显示的是网址导航的一句话描述...";
echo "</div>\r\n\t</div>\r\n</div>\r\n\r\n<section class=\"container\" id=\"navs\">\r\n\t<nav>\r\n\t\t<ul></ul>\r\n\t</nav>\r\n\t<div class=\"items\">\r\n\t\t";
wp_list_bookmarks(array("category" => $link_cat_ids, "show_description" => true, "between" => "<br>", "title_li" => __(""), "category_before" => "<div class=\"item\">", "category_after" => "</div>"));
echo "\t</div>\r\n</section>\r\n\r\n";

?>
