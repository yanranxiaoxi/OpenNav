<?php
/**
 * 用户控制器
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */

// 获取分页参数
$page = empty($_GET['page']) ? 'Options' : htmlspecialchars(trim($_GET['page']));


/**
 * 全局鉴权「Auth Safety」
 */
if (!$helper->isLogin()) {
	$data = [
		'code' => 403,
		'message' => '鉴权失败！',
		'data' => ''
	];
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}


/**
 * 进入用户设置流程
 */
if ($page === 'Options') {
	require_once('../Template/Admin/User/Options.php');
	exit();
}


/**
 * 保存用户设置
 */
if ($page === 'SetOptions') {
	$error_message = '';
	if (!empty($_POST['username']) && !empty($_POST['login_authentication_mode'])) {
		$email = empty($_POST['email']) ? '' : $_POST['email'];
		$login_authentication_mode = empty($_POST['login_authentication_mode']) ? 0 : intval($_POST['login_authentication_mode']);
		$username_regex = '/^[0-9a-zA-Z]{3,32}$/';
		$email_regex = '/^[0-9a-zA-Z_-]+@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)+$/';
		if (!preg_match($username_regex, $_POST['username'])) {
			$error_message = '用户名格式不正确！';
		} elseif (!empty($_POST['email']) && !preg_match($email_regex, $_POST['email'])) {
			$error_message = '电子邮箱格式不正确！';
		} elseif ($login_authentication_mode !== 3 && $login_authentication_mode !== 4 && $login_authentication_mode !== 5 && $login_authentication_mode !== 7) {
			$error_message = '验证方式的组合不合法！';
		} else {
			$helper->setGlobalConfig_AuthRequired('USERNAME', USERNAME, $_POST['username']);
			$helper->setGlobalConfig_AuthRequired('EMAIL', EMAIL, $email);
			$helper->setGlobalConfig_AuthRequired('LOGIN_AUTHENTICATION_MODE', LOGIN_AUTHENTICATION_MODE, $login_authentication_mode);
		}
	} else {
		$error_message = '设置失败！';
	}
	if (empty($error_message)) {
		$data = [
			'code' => 200,
			'message' => 'success'
		];
	} else {
		$data = [
			'code' => 403,
			'message' => $error_message
		];
	}
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}


/**
 * 进入密码设置流程
 */
if ($page === 'Password') {
	require_once('../Template/Admin/User/Password.php');
	exit();
}


/**
 * 设置密码
 */
if ($page === 'SetPassword') {
	$error_message = '';
	if (!empty($_POST['password'])) {
		$password_regex = '/^[0-9a-zA-Z!@#$%^&*()-_\[\]\{\}<>~`\+=,.;:\/?|]{6,128}$/';
		if (!preg_match($password_regex, $_POST['password'])) {
			$error_message = '密码格式不正确！';
		} else {
			$helper->setGlobalConfig_AuthRequired('PASSWORD', PASSWORD, password_hash($_POST['password'], PASSWORD_DEFAULT));
		}
	} else {
		$error_message = '密码不能为空！';
	}
	if (empty($error_message)) {
		$data = [
			'code' => 200,
			'message' => 'success'
		];
	} else {
		$data = [
			'code' => 403,
			'message' => $error_message
		];
	}
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}
