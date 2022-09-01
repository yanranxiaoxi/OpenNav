<?php
/**
 * 登录控制器
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */

use RobThree\Auth\TwoFactorAuth;

$authenticator = new TwoFactorAuth();

// 获取分页参数
$page = empty($_GET['page']) ? 'Login' : htmlspecialchars(trim($_GET['page']));


/**
 * 进入登录流程
 */
if ($page === 'Login') {
	if ($helper->isLogin()) {
		header('Location: ./index.php?c=Admin');
		exit();
	}
	require_once('../Template/Login.php');
	exit();
}


/**
 * 进入登录验证流程
 */
if ($page === 'Check') {
	// #TODO# 登录检查模式
	// 0 = 用户名 + 密码；
	// 1 = 用户名 + 密码 + TOTP Code；
	// 2 = (用户名 + TOTP Code) || (用户名 + 密码)；
	// 3 = TOTP Code || (用户名 + 密码)
	$login_check_mode = 0;
	$code = 0;
	$message = '';
	if ($login_check_mode === 0) {
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			if (USERNAME == $_POST['username'] && password_verify($_POST['password'], PASSWORD)) {
				$code = 200;
				$message = 'success';
			} else {
				$code = 403;
				$message = '用户名或密码不正确！';
			}
		} else {
			$code = 403;
			$message = '用户名或密码不能为空！';
		}
	} elseif ($login_check_mode === 1) {
		if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['totp_code'])) {
			if (USERNAME == $_POST['username'] && password_verify($_POST['password'], PASSWORD) && $authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])) {
				$code = 200;
				$message = 'success';
			} else {
				$code = 403;
				$message = '用户名、密码或 TOTP Code 不正确！';
			}
		} else {
			$code = 403;
			$message = '用户名、密码和 TOTP Code 不能为空！';
		}
	} elseif ($login_check_mode === 2) {
		if (!empty($_POST['username']) && !empty($_POST['totp_code'])) {
			if (USERNAME == $_POST['username'] && $authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])) {
				$code = 200;
				$message = 'success';
			} else {
				$code = 403;
				$message = '用户名或 TOTP Code 不正确！';
			}
		} elseif (!empty($_POST['username']) && !empty($_POST['password'])) {
			if (USERNAME == $_POST['username'] && password_verify($_POST['password'], PASSWORD)) {
				$code = 200;
				$message = 'success';
			} else {
				$code = 403;
				$message = '用户名或密码不正确！';
			}
		} else {
			$code = 403;
			$message = '请使用 (用户名 + TOTP Code) 或 (用户名 + 密码) 进行登录！';
		}
	} elseif ($login_check_mode === 3) {
		if (!empty($_POST['totp_code'])) {
			if ($authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])) {
				$code = 200;
				$message = 'success';
			} else {
				$code = 403;
				$message = 'TOTP Code 不正确！';
			}
		} elseif (!empty($_POST['username']) && !empty($_POST['password'])) {
			if (USERNAME == $_POST['username'] && password_verify($_POST['password'], PASSWORD)) {
				$code = 200;
				$message = 'success';
			} else {
				$code = 403;
				$message = '用户名或密码不正确！';
			}
		} else {
			$code = 403;
			$message = '请使用 TOTP Code 或 (用户名 + 密码) 进行登录！';
		}
	}
	if ($code === 200) {
		$helper->setLogin_AuthRequired();
	}
	$data = [
		'code' => $code,
		'message' => $message
	];
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}


/**
 * 进入注销流程
 */
if ($page === 'Logout') {
	if ($helper->removeLogin()) {
		header('Location: ./');
		exit();
	} else {
		exit('注销失败，请检查 Cookie 状态并手动清除！');
	}
}
