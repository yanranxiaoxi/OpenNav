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

require_once __DIR__ . '/../Public/index.php';

use RobThree\Auth\TwoFactorAuth;
use OpenNav\Class\GlobalHelper;

$helper = new GlobalHelper(null);

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
// 获取 PHP 插件列表
$php_extensions = get_loaded_extensions();

// 检查 PHP 版本是否支持
if (PHP_VERSION_ID < 80000) {
	exit('当前 PHP 版本' . PHP_VERSION . '不满足要求，需要 PHP ≥ 8.0');
}

// 检查 PHP 是否支持 SQLite (PDO) 插件
if (!array_search('pdo_sqlite', $php_extensions)) {
	exit('当前 PHP 未安装 pdo_sqlite 插件！');
}

// 检查 PHP 是否支持 Client URL 插件
if (!array_search('curl', $php_extensions)) {
	exit('当前 PHP 未安装 curl 插件！');
}

// 检查 PHP 是否支持 Multibyte String 插件
if (!array_search('mbstring', $php_extensions)) {
	exit('当前 PHP 未安装 mbstring 插件！');
}

// 检查 PHP 是否支持 Internationalization 插件
if (!array_search('intl', $php_extensions)) {
	exit('当前 PHP 未安装 intl 插件！');
}

/**
 * 进入初始化配置流程
 */
if ($page === 'Init') {
	require_once '../Template/Install.php';
	exit();
}

/**
 * 进入安装流程
 */
if ($page === 'Install') {
	if (!empty($_POST['username']) && !empty($_POST['password'])) {
		if (!$helper->validateUsername($_POST['username'])) {
			$helper->throwError(403, '用户名格式不正确！');
		} elseif (!$helper->validatePassword($_POST['password'])) {
			$helper->throwError(403, '密码格式不正确！');
		} elseif (!empty($_POST['email']) && !$helper->validateEmail($_POST['email'])) {
			$helper->throwError(403, '电子邮箱格式不正确！');
		} else {
			$authenticator = new TwoFactorAuth();
			$totp_secret_key = $authenticator->createSecret();
			$cookie_secret_key = $helper->getRandomKey();
			$config_file_content = file_get_contents('../Data/Config.sample.php');
			$config_file_content = str_replace(
				'{username}',
				$_POST['username'],
				$config_file_content
			);
			$config_file_content = str_replace(
				'{password}',
				password_hash($_POST['password'], PASSWORD_DEFAULT),
				$config_file_content
			);
			$config_file_content = str_replace('{email}', $_POST['email'], $config_file_content);
			$config_file_content = str_replace(
				'{totp_secret_key}',
				$totp_secret_key,
				$config_file_content
			);
			$config_file_content = str_replace(
				'{cookie_secret_key}',
				$cookie_secret_key,
				$config_file_content
			);
			if (!file_put_contents('../Data/Config.php', $config_file_content)) {
				$helper->throwError(403, '配置文件写入失败，请检查 Data 目录是否拥有写入权限！');
			}
		}
	} else {
		$helper->throwError(403, '用户名或密码不正确！');
	}
	$helper->returnSuccess();
}

exit();
