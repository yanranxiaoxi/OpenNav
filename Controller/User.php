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
if (!$is_login) {
	$helper->throwError(403, '鉴权失败！');
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
	if (!empty($_POST['username']) && !empty($_POST['login_authentication_mode'])) {
		$email = empty($_POST['email']) ? '' : $_POST['email'];
		$login_authentication_mode = empty($_POST['login_authentication_mode']) ? 0 : intval($_POST['login_authentication_mode']);
		$username_regex = '/^[0-9a-zA-Z]{3,32}$/';
		$email_regex = '/^[0-9a-zA-Z_-]+@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)+$/';
		if (!preg_match($username_regex, $_POST['username'])) {
			$helper->throwError(403, '用户名格式不正确！');
		} elseif (!empty($_POST['email']) && !preg_match($email_regex, $_POST['email'])) {
			$helper->throwError(403, '电子邮箱格式不正确！');
		} elseif ($login_authentication_mode !== 3 && $login_authentication_mode !== 4 && $login_authentication_mode !== 5 && $login_authentication_mode !== 7) {
			$helper->throwError(403, '验证方式的组合不合法！');
		} else {
			$helper->setGlobalConfig_AuthRequired('USERNAME', USERNAME, $_POST['username']);
			$helper->setGlobalConfig_AuthRequired('EMAIL', EMAIL, $email);
			$helper->setGlobalConfig_AuthRequired('LOGIN_AUTHENTICATION_MODE', LOGIN_AUTHENTICATION_MODE, $login_authentication_mode);
			$helper->returnSuccess();
		}
	} else {
		$helper->throwError(403, '设置失败！');
	}
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
	if (!empty($_POST['password'])) {
		$password_regex = '/^[0-9a-zA-Z!@#$%^&*()-_\[\]\{\}<>~`\+=,.;:\/?|]{6,128}$/';
		if (!preg_match($password_regex, $_POST['password'])) {
			$helper->throwError(403, '密码格式不正确！');
		} else {
			$helper->setGlobalConfig_AuthRequired('PASSWORD', PASSWORD, password_hash($_POST['password'], PASSWORD_DEFAULT));
			$helper->returnSuccess();
		}
	} else {
		$helper->throwError(403, '密码不能为空！');
	}
}


/**
 * 登出所有设备
 */
if ($page === 'LogoutAll') {
	$cookie_secret_key = $helper->getRandomKey();
	$helper->setGlobalConfig_AuthRequired('COOKIE_SECRET_KEY', COOKIE_SECRET_KEY, $cookie_secret_key);
	header('Location: ./index.php?c=Login');
	exit();
}
