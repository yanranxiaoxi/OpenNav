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
$page = empty($_GET['page']) ? 'Setup' : htmlspecialchars(trim($_GET['page']));


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
 * 重置 TOTP SecretKey
 */
if ($page === 'ResetSecretKey') {
	$totp_secret_key = $authenticator->createSecret();
	if ($helper->setGlobalConfig_AuthRequired('TOTP_SECRET_KEY', TOTP_SECRET_KEY, $totp_secret_key)) {
		$data = [
			'code' => 200,
			'message' => 'success'
		];
	} else {
		$data = [
			'code' => 403,
			'message' => '重置失败！'
		];
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
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}


/**
 * 进入配置 TOTP 流程
 */
if ($page === 'Setup') {
	$totp_data = 'otpauth://totp/OpenNav?secret=' . TOTP_SECRET_KEY;
	$totp_qrcode = $qrcode_generator->render($totp_data);
	require_once('../Template/Admin/Secure/TimeBaseValidator.php');
}
