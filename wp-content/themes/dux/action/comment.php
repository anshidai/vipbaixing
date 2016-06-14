<?php

function err($ErrMsg)
{
	header("HTTP/1.1 405 Method Not Allowed");
	echo $ErrMsg;
	exit();
}

if ("POST" != $_SERVER["REQUEST_METHOD"]) {
	header("Allow: POST");
	header("HTTP/1.1 405 Method Not Allowed");
	header("Content-Type: text/plain");
	exit();
}

require (dirname(__FILE__) . "/../../../../wp-load.php");
nocache_headers();
$comment_post_ID = (isset($_POST["comment_post_ID"]) ? (int) $_POST["comment_post_ID"] : 0);
$post = get_post($comment_post_ID);

if (empty($post->comment_status)) {
	do_action("comment_id_not_found", $comment_post_ID);
	err(__("Invalid comment status."));
}

$status = get_post_status($post);
$status_obj = get_post_status_object($status);
do_action("pre_comment_on_post", $comment_post_ID);
$comment_author = (isset($_POST["author"]) ? trim(strip_tags($_POST["author"])) : NULL);
$comment_author_email = (isset($_POST["email"]) ? trim($_POST["email"]) : NULL);
$comment_author_url = (isset($_POST["url"]) ? trim($_POST["url"]) : NULL);
$comment_content = (isset($_POST["comment"]) ? trim($_POST["comment"]) : NULL);
$edit_id = (isset($_POST["edit_id"]) ? $_POST["edit_id"] : NULL);
$user = wp_get_current_user();

if ($user->ID) {
	if ($user->display_name) {
		$user->display_name = $user->user_login;
	}

	$comment_author = $wpdb->escape($user->display_name);
	$comment_author_email = $wpdb->escape($user->user_email);
	$comment_author_url = $wpdb->escape($user->user_url);

	if (current_user_can("unfiltered_html")) {
		if (wp_create_nonce("unfiltered-html-comment_" . $comment_post_ID) != $_POST["_wp_unfiltered_html_comment"]) {
			kses_remove_filters();
			kses_init_filters();
		}
	}
}
else {
	if (get_option("comment_registration") || ("private" == $status)) {
		err("Hi，你必须登录才能发表评论！");
	}
}

$comment_type = "";
if (get_option("require_name_email") && !$user->ID) {
	if ((strlen($comment_author_email) < 6) || ("" == $comment_author)) {
		err("请填写昵称和邮箱！");
	}
	else if (!is_email($comment_author_email)) {
		err("请填写有效的邮箱地址！");
	}
}

if ("" == $comment_content) {
	err("请填写点评论！");
}

$comment_parent = ($_POST["comment_parent"] ? absint($_POST["comment_parent"]) : 0);
$commentdata = compact("comment_post_ID", "comment_author", "comment_author_email", "comment_author_url", "comment_content", "comment_type", "comment_parent", "user_ID");

if ($edit_id) {
	$comment_id = $commentdata["comment_ID"] = $edit_id;
	wp_update_comment($commentdata);
}
else {
	$comment_id = wp_new_comment($commentdata);
}

$comment = get_comment($comment_id);

if (!$user->ID) {
	$comment_cookie_lifetime = apply_filters("comment_cookie_lifetime", 30000000);
	setcookie("comment_author_" . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
	setcookie("comment_author_email_" . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
	setcookie("comment_author_url_" . COOKIEHASH, esc_url($comment->comment_author_url), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
}

$comment_depth = 1;
$tmp_c = $comment;

while ($tmp_c->comment_parent != 0) {
	$comment_depth++;
	$tmp_c = get_comment($tmp_c->comment_parent);
}

echo "<li ";
comment_class();
echo " id=\"comment-" . get_comment_id() . "\">";
echo "<span class=\"comt-f\">#</span>";
echo "<div class=\"comt-avatar\">";
echo _get_the_avatar($user_id = $comment->user_id, $user_email = $comment->comment_author_email, $src = true);
echo "</div>";
echo "<div class=\"comt-main\" id=\"div-comment-" . get_comment_id() . "\">";
echo str_replace(" src=", " data-src=", convert_smilies(get_comment_text()));
echo "<div class=\"comt-meta\"><span class=\"comt-author\">" . get_comment_author_link() . "</span>";
echo "<time>" . _get_time_ago($comment->comment_date) . "</time>";
echo "</div>";

if ($comment->comment_approved == "0") {
	echo "<span class=\"comt-approved\">待审核</span>";
}

echo "</div>";

?>
