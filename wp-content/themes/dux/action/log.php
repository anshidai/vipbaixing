<?php

if (!$_POST && !$_POST["action"]) {
	exit();
}

include ("load.php");

if (!_hui("user_page_s")) {
	exit();
}

$ui = array();

foreach ($_POST as $key => $value ) {
	$ui[$key] = $wpdb->escape(trim($value));
}

if (!$ui["action"]) {
	exit();
}

switch ($ui["action"]) {
case "signin":
	if (is_user_logged_in()) {
		print_r(json_encode(array("error" => 1, "msg" => "你已经登录")));
		exit();
	}

	if (!filter_var($ui["email"], FILTER_VALIDATE_EMAIL)) {
		print_r(json_encode(array("error" => 1, "msg" => "邮箱格式错误")));
		exit();
	}

	$user_data = get_user_by("email", $ui["email"]);

	if (empty($user_data)) {
		print_r(json_encode(array("error" => 1, "msg" => "邮箱或密码错误")));
		exit();
	}

	$ui["username"] = $user_data->user_login;

	if ($ui["remember"]) {
		$ui["remember"] = "true";
	}
	else {
		$ui["remember"] = "false";
	}

	$login_data = array("user_login" => $ui["username"], "user_password" => $ui["password"], "remember" => $ui["remember"]);
	$user_verify = wp_signon($login_data, false);

	if (is_wp_error($user_verify)) {
		print_r(json_encode(array("error" => 1, "msg" => "邮箱或密码错误")));
		exit();
	}

	print_r(json_encode(array("error" => 0)));
	exit();
	break;

case "signup":
	if (is_user_logged_in()) {
		print_r(json_encode(array("error" => 1, "msg" => "你已经登录")));
		exit();
	}

	if ((sstrlen($ui["name"]) < 2) || (12 < sstrlen($ui["name"]))) {
		print_r(json_encode(array("error" => 1, "msg" => "昵称限制2-12位")));
		exit();
	}

	if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $ui["email"])) {
		print_r(json_encode(array("error" => 1, "msg" => "邮箱格式错误")));
		exit();
	}

	if (sstrlen($ui["password"]) < 6) {
		print_r(json_encode(array("error" => 1, "msg" => "密码太短")));
		exit();
	}

	$uname = "u" . get_millisecond() . rand(1000, 9999);
	$status = wp_create_user($uname, $ui["password"], $ui["email"]);

	if (is_wp_error($status)) {
		if ($status->errors["existing_user_email"]) {
			print_r(json_encode(array("error" => 1, "msg" => "邮箱已存在，换一个试试")));
			exit();
		}

		print_r(json_encode(array("error" => 1, "msg" => "注册失败，请稍后再试")));
		exit();
	}

	if ($status) {
		wp_update_user(array("ID" => $status, "display_name" => $ui["name"]));
		$login_data = array("user_login" => $uname, "user_password" => $ui["password"], "remember" => true);
		$user_verify = wp_signon($login_data, true);
		_moloader("mo_get_user_page", false);
		print_r(json_encode(array("error" => 0, "goto" => mo_get_user_page())));
	}

	exit();
	break;

case "password":
	if (!is_user_logged_in()) {
		print_r(json_encode(array("error" => 1, "msg" => "必须登录才能操作")));
		exit();
	}

	if (!$ui["passwordold"] && !$ui["password"] && !$ui["password2"]) {
		print_r(json_encode(array("error" => 1, "msg" => "密码不能为空")));
		exit();
	}

	if (strlen($ui["password"]) < 6) {
		print_r(json_encode(array("error" => 1, "msg" => "密码至少6位")));
		exit();
	}

	if ($ui["password"] !== $ui["password2"]) {
		print_r(json_encode(array("error" => 1, "msg" => "两次密码输入不一致")));
		exit();
	}

	if ($ui["passwordold"] == $ui["password"]) {
		print_r(json_encode(array("error" => 1, "msg" => "新密码和原密码不能相同")));
		exit();
	}

	$uid = get_current_user_id();
	global $wp_hasher;
	require_once (ABSPATH . WPINC . "/class-phpass.php");
	$wp_hasher = new PasswordHash(8, true);

	if (!$wp_hasher->CheckPassword($ui["passwordold"], $current_user->user_pass)) {
		print_r(json_encode(array("error" => 1, "msg" => "原密码错误")));
		exit();
	}

	require_once (ABSPATH . WPINC . "/registration.php");
	$status = wp_update_user(array("ID" => $uid, "user_pass" => $ui["password"]));

	if (is_wp_error($status)) {
		print_r(json_encode(array("error" => 1, "msg" => "修改失败，请稍后再试")));
		exit();
	}

	print_r(json_encode(array("error" => 0)));
	exit();
	break;

default:
	break;
}

exit();

?>
