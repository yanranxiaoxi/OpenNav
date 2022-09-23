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

// 获取分页参数
$page = empty($_GET['page']) ? 'Setup' : htmlspecialchars(trim($_GET['page']));


/**
 * 全局鉴权「Auth Safety」
 */
if (!$is_login) {
	$helper->throwError(403, '鉴权失败！');
}


/**
 * 重置 TOTP SecretKey
 */
if ($page === 'ResetSecretKey') {
	$totp_secret_key = $authenticator->createSecret();
	if ($helper->setGlobalConfig_AuthRequired('TOTP_SECRET_KEY', TOTP_SECRET_KEY, $totp_secret_key)) {
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, '重置失败！');
	}
}


/**
 * 验证 TOTP Code
 */
if ($page === 'VerifyCode') {
	if (!empty($_POST['totp_code'])) {
		if ($authenticator->verifyCode(TOTP_SECRET_KEY, $_POST['totp_code'])) {
			$helper->returnSuccess();
		} else {
			$helper->throwError(403, '验证失败！');
		}
	} else {
		$helper->throwError(403, '必要参数 totp_code 不存在！');
	}
}


/**
 * 进入配置 TOTP 流程
 */
if ($page === 'Setup') {
	$qrcode_options = new QROptions([
		'version'    => 5,
		'outputType' => QRCode::OUTPUT_MARKUP_SVG,
		'eccLevel'   => QRCode::ECC_L
	]);
	$qrcode_generator = new QRCode($qrcode_options);
	$totp_data = 'otpauth://totp/OpenNav?secret=' . TOTP_SECRET_KEY;
	$totp_qrcode = $qrcode_generator->render($totp_data);
	require_once('../Template/Admin/Secure/TimeBaseValidator.php');
	exit();
}
