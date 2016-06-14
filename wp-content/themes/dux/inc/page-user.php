<?php

if (!_hui("user_page_s")) {
	exit("该功能需要开启！");
}

echo "<section class=\"container\">\r\n\t<div class=\"container-user\"";
echo is_user_logged_in() ? "" : " id=\"issignshow\" style=\"height:500px;\"";
echo ">\r\n\t\t";

if (is_user_logged_in()) {
	global $current_user;
	echo "\t\t<div class=\"userside\">\r\n\t\t\t<div class=\"usertitle\">\r\n\t\t\t\t";
	echo _get_the_avatar($user_id = $current_user->ID, $user_email = $current_user->user_email, true);
	echo "\t\t\t\t<h2>";
	echo $current_user->display_name;
	echo "</h2>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"usermenus\">\t\r\n\t\t\t\t<ul class=\"usermenu\">\r\n\t\t\t\t\t<li class=\"usermenu-posts\"><a href=\"#posts/all\">我的文章</a></li>\r\n\t\t\t\t\t<li class=\"usermenu-comments\"><a href=\"#comments\">我的评论</a></li>\r\n\t\t\t\t\t<li class=\"usermenu-info\"><a href=\"#info\">修改资料</a></li>\r\n\t\t\t\t\t<li class=\"usermenu-password\"><a href=\"#password\">修改密码</a></li>\r\n\t\t\t\t\t<li class=\"usermenu-signout\"><a href=\"";
	echo wp_logout_url(home_url());
	echo "\">退出</a></li>\r\n\t\t\t\t</ul>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"content\" id=\"contentframe\">\r\n\t\t\t<div class=\"user-main\"></div>\r\n\t\t\t<div class=\"user-tips\"></div>\r\n\t\t</div>\r\n\t\t";
}

echo "\t</div>\r\n</section>\r\n\r\n";

if (is_user_logged_in()) {
	echo "<script id=\"temp-postmenu\" type=\"text/x-jsrender\">\r\n\t<a href=\"#posts/{{>name}}\">{{>title}}<small>({{>count}})</small></a>\r\n</script>\r\n\r\n<script id=\"temp-postitem\" type=\"text/x-jsrender\">\r\n\t<li>\r\n\t\t<img data-src=\"{{>thumb}}\" class=\"thumb\">\r\n\t\t<h2><a target=\"_blank\" href=\"{{>link}}\">{{>title}}</a></h2>\r\n\t\t<p class=\"note\">{{>desc}}</p>\r\n\t\t<p class=\"text-muted\">{{>time}} &nbsp;&nbsp; 分类：{{>cat}} &nbsp;&nbsp; 阅读({{>view}}) &nbsp;&nbsp; 评论({{>comment}}) &nbsp;&nbsp; 赞({{>like}})</p>\r\n\t</li>\r\n</script>\r\n\r\n<script id=\"temp-info\" type=\"text/x-jsrender\">\r\n\t<form>\r\n\t  \t<ul class=\"user-meta\">\r\n\t  \t\t<li><label>入门时间</label>\r\n\t\t\t\t{{>regtime}}\r\n\t  \t\t</li>\r\n\t  \t\t<li><label>昵称</label>\r\n\t\t\t\t<input type=\"input\" class=\"form-control\" name=\"nickname\" value=\"{{>nickname}}\">\r\n\t  \t\t</li>\r\n\t  \t\t<li><label>邮箱</label>\r\n\t\t\t\t<input type=\"email\" class=\"form-control\" name=\"email\" value=\"{{>email}}\">\r\n\t  \t\t</li>\r\n\t  \t\t<li><label>网址</label>\r\n\t\t\t\t<input type=\"input\" class=\"form-control\" name=\"url\" value=\"{{>url}}\">\r\n\t  \t\t</li>\r\n\t  \t\t<li><label>QQ</label>\r\n\t\t\t\t<input type=\"input\" class=\"form-control\" name=\"qq\" value=\"{{>qq}}\">\r\n\t  \t\t</li>\r\n\t  \t\t<li><label>微信号</label>\r\n\t\t\t\t<input type=\"input\" class=\"form-control\" name=\"weixin\" value=\"{{>weixin}}\">\r\n\t  \t\t</li>\r\n\t  \t\t<li><label>微博地址</label>\r\n\t\t\t\t<input type=\"input\" class=\"form-control\" name=\"weibo\" value=\"{{>weibo}}\">\r\n\t  \t\t</li>\r\n\t  \t\t<li>\r\n\t\t\t\t<input type=\"button\" evt=\"info.submit\" class=\"btn btn-primary\" name=\"submit\" value=\"确认修改资料\">\r\n\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"info.edit\">\r\n\t  \t\t</li>\r\n\t  \t</ul>\r\n\t</form>\r\n</script>\r\n\r\n<script id=\"temp-password\" type=\"text/x-jsrender\">\r\n\t<form>\r\n\t  \t<ul class=\"user-meta\">\r\n\t  \t\t<li><label>原密码</label>\r\n\t\t\t\t<input type=\"password\" class=\"form-control\" name=\"passwordold\">\r\n\t  \t\t</li>\r\n\t  \t\t<li><label>新密码</label>\r\n\t\t\t\t<input type=\"password\" class=\"form-control\" name=\"password\">\r\n\t  \t\t</li>\r\n\t  \t\t<li><label>重复新密码</label>\r\n\t\t\t\t<input type=\"password\" class=\"form-control\" name=\"password2\">\r\n\t  \t\t</li>\r\n\t  \t\t<li>\r\n\t\t\t\t<input type=\"button\" evt=\"password.submit\" class=\"btn btn-primary\" name=\"submit\" value=\"确认修改密码\">\r\n\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"password.edit\">\r\n\t  \t\t</li>\r\n\t  \t</ul>\r\n\t</form>\r\n</script>\r\n\r\n<script id=\"temp-commentitem\" type=\"text/x-jsrender\">\r\n\t<li>\r\n\t\t<time>{{>time}}</time>\r\n\t\t<p class=\"note\">{{>content}}</p>\r\n\t\t<p class=\"text-muted\">文章：<a target=\"_blank\" href=\"{{>post_link}}\">{{>post_title}}</a></p>\r\n\t</li>\r\n</script>\r\n\r\n";
}

?>
