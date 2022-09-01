<?php
/**
 * 二次验证控制器
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
$page = empty($_GET['page']) ? '' : htmlspecialchars(trim($_GET['page']));


/**
 * 获取 TOTP SecretKey
 */
if ($page === 'GetSecretKey') {
	if (!defined('TOTP_SECRET_KEY')) {
		$totp_secret_key = $authenticator->createSecret();
		$data = [
			'code' => 200,
			'message' => '成功获取 TOTP SecretKey',
			'data' => $totp_secret_key
		];
	} else {
		if (empty(TOTP_SECRET_KEY)) {
			$totp_secret_key = $authenticator->createSecret();
			$data = [
				'code' => 200,
				'message' => '成功获取 TOTP SecretKey',
				'data' => $totp_secret_key
			];
		} else {
			$data = [
				'code' => 403,
				'message' => '当前状态不允许获取 TOTP SecretKey',
				'data' => ''
			];
		}
	}
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}


/**
 * 验证 TOTP Code
 */
if ($page === 'VerifyCode') {
	if (!empty($_POST['totp_code'])) {
		if ($authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])) {
			$data = [
				'code' => 200,
				'message' => '验证成功',
				'data' => true
			];
		} else {
			$data = [
				'code' => 403,
				'message' => '验证失败',
				'data' => false
			];
		}
	} else {
		$data = [
			'code' => 404,
			'message' => '必要参数 totp_code 不存在',
			'data' => false
		];
	}
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}
