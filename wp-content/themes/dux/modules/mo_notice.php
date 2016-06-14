<?php

if (is_home() && (_hui("site_notice_s") || _hui("user_on_notice_s"))) {
	$s_notice = _hui("site_notice_s");
	$s_user = _hui("user_page_s") && _hui("user_on_notice_s");
	_moloader("mo_get_user_page", false);
	echo "\t<div class=\"widget widget-tops\">\r\n\t\t<ul class=\"widget-nav\">\r\n\t\t\t";

	if ($s_notice) {
		echo "<li";
		echo $s_notice ? " class=\"active\"" : "";
		echo ">";
		echo _hui("site_notice_title") ? _hui("site_notice_title") : "网站公告";
		echo "</li>";
	}

	echo "\t\t\t";

	if ($s_user) {
		echo "<li";
		echo $s_user && !$s_notice ? " class=\"active\"" : "";
		echo ">会员中心</li>";
	}

	echo "\t\t</ul>\r\n\t\t<ul class=\"widget-navcontent\">\r\n\t\t\t";
	if ($s_notice && _hui("site_notice_cat")) {
		echo "\t\t\t\t<li class=\"item item-01";
		echo $s_notice ? " active" : "";
		echo "\">\r\n\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t";
		$args = array("ignore_sticky_posts" => 1, "showposts" => 5, "cat" => _hui("site_notice_cat"));
		query_posts($args);

		while (have_posts()) {
			the_post();
			echo "<li><time>" . get_the_time("m-d") . "</time><a target=\"_blank\" href=\"" . get_permalink() . "\">" . get_the_title() . "</a></li>";
		}

		wp_reset_query();
		echo "\t\t\t\t\t</ul>\r\n\t\t\t\t</li>\r\n\t\t\t";
	}

	echo "\t\t\t";

	if ($s_user) {
		echo "\t\t\t\t<li class=\"item item-02";
		echo $s_user && !$s_notice ? " active" : "";
		echo "\">\r\n\t\t\t\t\t";

		if (is_user_logged_in()) {
			global $current_user;
			echo "\t\t\t\t\t\t<dl>\r\n\t\t\t\t\t\t\t<dt>";
			echo _get_the_avatar($user_id = $current_user->ID, $user_email = $current_user->user_email, true);
			echo "</dt>\r\n\t\t\t\t\t\t\t<dd>";
			echo $current_user->display_name;
			echo "<span class=\"text-muted\">";
			echo $current_user->user_email;
			echo "</span></dd>\r\n\t\t\t\t\t\t</dl>\r\n\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t<li><a href=\"";
			echo mo_get_user_page() . "#posts/all";
			echo "\">我的文章</a></li>\r\n\t\t\t\t\t\t\t<li><a href=\"";
			echo mo_get_user_page() . "#comments";
			echo "\">我的评论</a></li>\r\n\t\t\t\t\t\t\t<li><a href=\"";
			echo mo_get_user_page() . "#info";
			echo "\">修改资料</a></li>\r\n\t\t\t\t\t\t\t<li><a href=\"";
			echo mo_get_user_page() . "#password";
			echo "\">修改密码</a></li>\r\n\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t";
		}
		else {
			echo "\t\t\t\t\t\t<h4>需要登录才能进入会员中心</h4>\r\n\t\t\t\t\t\t<p>\r\n\t\t\t\t\t\t\t<a href=\"javascript:;\" class=\"btn btn-primary signin-loader\">立即登录</a>\r\n\t\t\t\t\t\t\t<a href=\"javascript:;\" class=\"btn btn-default signup-loader\">现在注册</a>\r\n\t\t\t\t\t\t</p>\r\n\t\t\t\t\t";
		}

		echo "\t\t\t\t</li>\r\n\t\t\t";
	}

	echo "\t\t</ul>\r\n\t</div>\r\n";
}

?>
