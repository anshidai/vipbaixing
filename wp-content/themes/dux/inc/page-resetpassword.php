<?php

function errormsg($wp_error = "")
{
	if (empty($wp_error)) {
		$wp_error = new WP_Error();
	}

	if ($wp_error->get_error_code()) {
		$errors = "";
		$messages = "";

		foreach ($wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data($code);

			foreach ($wp_error->get_error_messages($code) as $error ) {
				if ("message" == $severity) {
					$messages .= "\t" . $error . "<br />\n";
				}
				else {
					$errors .= "\t" . $error . "<br />\n";
				}
			}
		}

		if (!empty($errors)) {
			echo "<p class=\"errtip\">" . apply_filters("login_errors", $errors) . "</p>\n";
		}

		if (!empty($messages)) {
			echo "<p class=\"errtip\">" . apply_filters("login_messages", $messages) . "</p>\n";
		}
	}
}

function retrieve_password()
{
	global $wpdb;
	global $wp_hasher;
	$errors = new WP_Error();

	if (empty($_POST["user_login"])) {
		$errors->add("empty_username", __("<strong>ERROR</strong>: Enter a username or e-mail address."));
	}
	else if (strpos($_POST["user_login"], "@")) {
		$user_data = get_user_by("email", trim($_POST["user_login"]));

		if (empty($user_data)) {
			$errors->add("invalid_email", __("<strong>ERROR</strong>: There is no user registered with that email address."));
		}
	}
	else {
		$login = trim($_POST["user_login"]);
		$user_data = get_user_by("login", $login);
	}

	do_action("lostpassword_post");

	if ($errors->get_error_code()) {
		return $errors;
	}

	if (!$user_data) {
		$errors->add("invalidcombo", __("<strong>ERROR</strong>: Invalid username or e-mail."));
		return $errors;
	}

	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	do_action("retreive_password", $user_login);
	do_action("retrieve_password", $user_login);
	$allow = apply_filters("allow_password_reset", true, $user_data->ID);

	if (!$allow) {
		return new WP_Error("no_password_reset", __("Password reset is not allowed for this user"));
	}
	else if (is_wp_error($allow)) {
		return $allow;
	}

	$key = wp_generate_password(20, false);
	do_action("retrieve_password_key", $user_login, $key);

	if (empty($wp_hasher)) {
		require_once (ABSPATH . "wp-includes/class-phpass.php");
		$wp_hasher = new PasswordHash(8, true);
	}

	$hashed = $wp_hasher->HashPassword($key);
	$wpdb->update($wpdb->users, array("user_activation_key" => $hashed), array("user_login" => $user_login));
	$message = __("Someone requested that the password be reset for the following account:") . "\r\n\r\n";
	$message .= network_home_url("/") . "\r\n\r\n";
	$message .= sprintf(__("Username: %s"), $user_login) . "\r\n\r\n";
	$message .= __("If this was a mistake, just ignore this email and nothing will happen.") . "\r\n\r\n";
	$message .= __("To reset your password, visit the following address:") . "\r\n\r\n";
	$message .= network_site_url(mo_get_user_rp() . "?action=resetpass&key=$key&login=" . rawurlencode($user_login), "login");
	$message = str_replace(site_url("/") . site_url("/"), site_url("/"), $message);

	if (is_multisite()) {
		$blogname = $GLOBALS["current_site"]->site_name;
	}
	else {
		$blogname = wp_specialchars_decode(get_option("blogname"), ENT_QUOTES);
	}

	$title = sprintf(__("[%s] Password Reset"), $blogname);
	$title = apply_filters("retrieve_password_title", $title);
	$message = apply_filters("retrieve_password_message", $message, $key);
	if ($message && !wp_mail($user_email, $title, $message)) {
		exit(__("The e-mail could not be sent.") . "<br />\n" . __("Possible reason: your host may have disabled the mail() function."));
	}

	return true;
}

_moloader("mo_get_user_rp", false);
$http_post = "POST" == $_SERVER["REQUEST_METHOD"];
$action = ($_REQUEST["action"] ? $_REQUEST["action"] : "lostpassword");

if ($_REQUEST["key"]) {
	$action = "resetpass";
}

if (!in_array($action, array("lostpassword", "resetpass", "success"), true)) {
	$action = "lostpassword";
}

switch ($action) {
case "lostpassword":
	$errors = new WP_Error();

	if ($http_post) {
		$errors = retrieve_password();
	}

	if ($_REQUEST["error"]) {
		if ("invalidkey" == $_REQUEST["error"]) {
			$errors->add("invalidkey", __("Sorry, that key does not appear to be valid."));
		}
		else if ("expiredkey" == $_REQUEST["error"]) {
			$errors->add("expiredkey", __("Sorry, that key has expired. Please try again."));
		}
	}

	$classactive1 = " class=\"active\"";
	break;

case "resetpass":
	$user = check_password_reset_key($_REQUEST["key"], $_REQUEST["login"]);

	if (is_wp_error($user)) {
		if ($user->get_error_code() === "expired_key") {
			wp_redirect(mo_get_user_rp() . "?action=lostpassword&error=expiredkey");
		}
		else {
			wp_redirect(mo_get_user_rp() . "?action=lostpassword&error=invalidkey");
		}

		exit();
	}

	$errors = new WP_Error();
	if ($_POST["pass1"] && ($_POST["pass1"] != $_POST["pass2"])) {
		$errors->add("password_reset_mismatch", __("The passwords do not match."));
	}

	if (strlen($_POST["pass1"]) < 6) {
		$errors->add("password_reset_mismatch2", "密码至少6位。");
	}

	do_action("validate_password_reset", $errors, $user);
	if (!$errors->get_error_code() && $_POST["pass1"] && !$_POST["pass1"]) {
		reset_password($user, $_POST["pass1"]);
		wp_redirect(mo_get_user_rp() . "?action=success");
		exit();
	}

	$classactive2 = " class=\"active\"";
	break;

case "success":
	$classactive3 = " class=\"active\"";
	break;
}

echo "<section class=\"container\">\t\r\n<div class=\"content-wrap\">\r\n\t<div class=\"content resetpass\">\r\n\t\t<h1 class=\"hide\">";
the_title();
echo "</h1>\r\n\t\t<ul class=\"resetpasssteps\">\r\n\t\t\t<li";
echo $classactive1;
echo ">获取密码重置邮件<span class=\"glyphicon glyphicon-chevron-right\"></span></li>\r\n\t\t\t<li";
echo $classactive2;
echo ">设置新密码<span class=\"glyphicon glyphicon-chevron-right\"></span></li>\r\n\t\t\t<li";
echo $classactive3;
echo ">成功修改密码</li>\r\n\t\t</ul>\r\n\t\t\r\n\t\t";

if ($classactive1) {
	if ($errors !== true) {
		echo "\t\t<form action=\"";
		echo esc_url(mo_get_user_rp() . "?action=lostpassword");
		echo "\" method=\"post\">\r\n\t\t\t";
		errormsg($errors);
		echo "\t\t\t<h3>填写用户名或邮箱：</h3>\r\n\t\t\t<p><input type=\"text\" name=\"user_login\" class=\"form-control input-lg\" placeholder=\"用户名或邮箱\" autofocus></p>\r\n\t\t\t<p><input type=\"submit\" value=\"获取密码重置邮件\" class=\"btn btn-block btn-primary btn-lg\"></p>\r\n\t\t</form>\r\n\t\t";
	}
	else {
		echo "<h3><span class=\"text-success\">已向注册邮箱发送邮件！</span></h3>";
		echo "<p>去邮箱查收邮件并点击重置密码链接</p>";
	}
}

echo "\r\n\t\t";

if ($classactive2) {
	echo "\t\t<form action=\"\" method=\"post\">\r\n\t\t\t";
	errormsg($errors);
	echo "\t\t\t<h3>设置新密码：</h3>\r\n\t\t\t<p><input type=\"password\" name=\"pass1\" class=\"form-control input-lg\" placeholder=\"输入新密码\" autofocus></p>\r\n\t\t\t<h5>重复新密码：</h5>\r\n\t\t\t<p><input type=\"password\" name=\"pass2\" class=\"form-control input-lg\" placeholder=\"重复新密码\"></p>\r\n\t\t\t<p><input type=\"submit\" value=\"确认提交\" class=\"btn btn-block btn-primary btn-lg\"></p>\r\n\t\t</form>\r\n\t\t";
}

echo "\r\n\t\t";

if ($classactive3) {
	echo "\t\t<form>\r\n\t\t\t<h3><span class=\"text-success\"><span class=\"glyphicon glyphicon-ok-circle\"></span> 恭喜，您的密码已重置！</span></h3>\r\n\t\t\t<p> &nbsp; </p>\r\n\t\t\t<p class=\"text-center\"><a class=\"btn btn-success btn-lg\" href=\"";
	echo get_bloginfo("url");
	echo "\">回首页</a></p>\r\n\t\t</form>\r\n\t\t";
}

echo "\r\n\t</div>\r\n</div>\r\n</section>\r\n";

?>
