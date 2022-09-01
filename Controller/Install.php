<?php
/**
 * 初始化安装控制器
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
$page = empty($_GET['page']) ? 'Init' : htmlspecialchars(trim($_GET['page']));


/**
 * 检查实例是否已初始化安装
 */
if (file_exists('../Data/Config.php')) {
	exit('该 OpenNav 实例已安装成功，无需再次执行安装操作！');
}


/**
 * 检查运行环境
 */
// 获取 PHP 版本
$php_version = floatval(PHP_VERSION);
// 获取 PHP 插件列表
$php_extensions = get_loaded_extensions();

// 检查 PHP 版本是否支持
if ($php_version <= 5.6) {
	exit('当前 PHP 版本' . $php_version . '不满足要求，需要 PHP ≥ 5.6');
}

// 检查 PHP 是否支持 PDO_SQLITE 插件
if (!array_search('pdo_sqlite', $php_extensions)) {
	exit('当前 PHP 未安装 PDO_SQLITE 插件！');
}

// 检查 PHP 是否支持 cURL 插件
if (!array_search('curl', $php_extensions)) {
	exit('当前 PHP 未安装 cURL 插件！');
}


/**
 * 进入初始化配置流程
 */
if ($page === 'Init') {
	require_once('../Template/Install.php');
	exit();
}


/**
 * 进入安装流程
 */
if ($page === 'Install') {
	$error_message = '';
	if (!empty($_POST['username']) && !empty($_POST['password'])) {
		$username_regex = '/^[0-9a-zA-Z]{3,32}$/';
		$password_regex = '/^[0-9a-zA-Z!@#$%^&*()-_\[\]\{\}<>~`\+=,.;:\/?|]{6,128}$/';
		$email_regex = '/^[0-9a-zA-Z_-]+@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)+$/';
		if (!preg_match($username_regex, $_POST['username'])) {
			$error_message = '用户名格式不正确！';
		} elseif (!preg_match($password_regex, $_POST['password'])) {
			$error_message = '密码格式不正确！';
		} elseif (!empty($_POST['email']) && !preg_match($email_regex, $_POST['email'])) {
			$error_message = '电子邮箱格式不正确！';
		} else {
			$totp_secret_key = $authenticator->createSecret();
			$cookie_secret_key = $helper->getRandomKey();
			$config_file_content = file_get_contents('../Data/Config.sample.php');
			$config_file_content = str_replace('{username}', $_POST['username'], $config_file_content);
			$config_file_content = str_replace('{password}', password_hash($_POST['password'], PASSWORD_DEFAULT), $config_file_content);
			$config_file_content = str_replace('{email}', $_POST['email'], $config_file_content);
			$config_file_content = str_replace('{totp_secret_key}', $totp_secret_key, $config_file_content);
			$config_file_content = str_replace('{cookie_secret_key}', $cookie_secret_key, $config_file_content);
			if (!file_put_contents('../Data/Config.php', $config_file_content)) {
				$error_message = '配置文件写入失败，请检查 Data 目录是否拥有写入权限！';
			}
		}
	} else {
		$error_message = '用户名或密码不正确！';
	}
	if (empty($error_message)) {
		$data = [
			'code' => 200,
			'message' => '安装成功！请等待页面跳转 ...'
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
