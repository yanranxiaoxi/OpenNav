<?php
/**
 * 时基验证控制器
 * 进入配置 TOTP 流程需要 mbstring 支持
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */

use RobThree\Auth\TwoFactorAuth;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$authenticator = new TwoFactorAuth();
$qrcode_options = new QROptions([
	'version'    => 5,
	'outputType' => QRCode::OUTPUT_MARKUP_SVG,
	'eccLevel'   => QRCode::ECC_L
]);
$qrcode_generator = new QRCode($qrcode_options);

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
	if ($helper->isLogin()) {
		if (!empty($_POST['totp_code'])) {
			if ($authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])) {
				$data = [
					'code' => 200,
					'message' => '验证成功！',
					'data' => true
				];
			} else {
				$data = [
					'code' => 403,
					'message' => '验证失败！',
					'data' => false
				];
			}
		} else {
			$data = [
				'code' => 404,
				'message' => '必要参数 totp_code 不存在！',
				'data' => false
			];
		}
	} else {
		$data = [
			'code' => 403,
			'message' => '鉴权失败！',
			'data' => false
		];
	}
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}


/**
 * 获取 TOTP QRCode
 * 此处将会返回 Base64 编码的 SVG 图像
 */
if ($page === 'GetQRCode') {
	if ($helper->isLogin()) {
		$totp_data = 'otpauth://totp/OpenNav?secret=' . TOTP_SECRET_KEY;
		header('Content-Type: text/plain; charset=utf-8');
		exit($qrcode_generator->render($totp_data));
	} else {
		exit('鉴权失败！');
	}
}


/**
 * 进入配置 TOTP 流程
 */
if ($page === 'Setup') {
	if ($helper->isLogin()) {
		$totp_data = 'otpauth://totp/OpenNav?secret=' . TOTP_SECRET_KEY;
		$totp_qrcode = $qrcode_generator->render($totp_data);
		require_once('../Template/Admin/Secure/TimeBaseValidator.php');
	} else {
		exit('鉴权失败！');
	}
}
