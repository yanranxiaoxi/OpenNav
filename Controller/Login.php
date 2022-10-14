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

require_once __DIR__ . '/../Public/index.php';

use RobThree\Auth\TwoFactorAuth;

// 获取分页参数
$page = empty($_GET['page']) ? 'Login' : htmlspecialchars(trim($_GET['page']));

/**
 * 进入登录流程
 */
if ($page === 'Login') {
	if ($is_login) {
		header('Location: ./index.php?c=Admin');
		exit();
	}
	require_once '../Template/Login.php';
	exit();
}

/**
 * 进入登录验证流程
 */
if ($page === 'Check') {
	$authenticator = new TwoFactorAuth();
	if (LOGIN_AUTHENTICATION_MODE === 3) {
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			if (USERNAME == $_POST['username'] && password_verify($_POST['password'], PASSWORD)) {
				$helper->setLogin_AuthRequired();
				$helper->returnSuccess();
			} else {
				$helper->throwError(403, '用户名或密码不正确！');
			}
		} else {
			$helper->throwError(403, '用户名或密码不能为空！');
		}
	} elseif (LOGIN_AUTHENTICATION_MODE === 7) {
		if (
			!empty($_POST['username']) &&
			!empty($_POST['password']) &&
			!empty($_POST['totp_code'])
		) {
			if (
				USERNAME == $_POST['username'] &&
				password_verify($_POST['password'], PASSWORD) &&
				$authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])
			) {
				$helper->setLogin_AuthRequired();
				$helper->returnSuccess();
			} else {
				$helper->throwError(403, '用户名、密码或 TOTP Code 不正确！');
			}
		} else {
			$helper->throwError(403, '用户名、密码和 TOTP Code 不能为空！');
		}
	} elseif (LOGIN_AUTHENTICATION_MODE === 5) {
		if (!empty($_POST['username']) && !empty($_POST['totp_code'])) {
			if (
				USERNAME == $_POST['username'] &&
				$authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])
			) {
				$helper->setLogin_AuthRequired();
				$helper->returnSuccess();
			} else {
				$helper->throwError(403, '用户名或 TOTP Code 不正确！');
			}
		} elseif (!empty($_POST['username']) && !empty($_POST['password'])) {
			if (USERNAME == $_POST['username'] && password_verify($_POST['password'], PASSWORD)) {
				$helper->setLogin_AuthRequired();
				$helper->returnSuccess();
			} else {
				$helper->throwError(403, '用户名或密码不正确！');
			}
		} else {
			$helper->throwError(403, '请使用 (用户名 + TOTP Code) 或 (用户名 + 密码) 进行登录！');
		}
	} elseif (LOGIN_AUTHENTICATION_MODE === 4) {
		if (!empty($_POST['totp_code'])) {
			if ($authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])) {
				$helper->setLoginByOnlyTimeBaseValidator_AuthRequired();
				$data = [
					'code' => 201,
					'message' => 'totp success'
				];
				$helper->returnSuccess($data);
			} else {
				$helper->throwError(403, 'TOTP Code 不正确！');
			}
		} elseif (!empty($_POST['username']) && !empty($_POST['password'])) {
			if (USERNAME == $_POST['username'] && password_verify($_POST['password'], PASSWORD)) {
				$helper->setLogin_AuthRequired();
				$helper->returnSuccess();
			} else {
				$helper->throwError(403, '用户名或密码不正确！');
			}
		} else {
			$helper->throwError(403, '请使用 TOTP Code 或 (用户名 + 密码) 进行登录！');
		}
	}
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

exit();
