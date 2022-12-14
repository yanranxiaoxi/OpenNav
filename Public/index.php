<?php
/**
 * OpenNav 全局入口文件
 *
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 *
 * @link		https://opennav.soraharu.com/
 */

declare(strict_types=1);

namespace OpenNav\Public;

/**
 * 载入前置文件
 */
require_once '../vendor/autoload.php';
require_once '../Class/GlobalHelper.php';
require_once '../Binary/SystemConfig.php';

use Medoo\Medoo;
use OpenNav\Class\GlobalHelper;

/**
 * 载入配置文件
 */
// 如果配置文件不存在，则载入初始化控制器
if (!file_exists('../Data/Config.php')) {
	require_once '../Controller/Install.php';
	exit();
}
require_once '../Data/Config.php';
// 连接数据库
if (DATABASE_TYPE === 'MariaDB' || DATABASE_TYPE === 'MySQL') {
	$database = new Medoo([
		'type' => 'mysql',
		'host' => DATABASE_HOST,
		'port' => DATABASE_PORT,
		'database' => DATABASE_NAME,
		'username' => DATABASE_USERNAME,
		'password' => DATABASE_PASSWORD
	]);
} elseif (DATABASE_TYPE === 'MSSQL') {
	$database = new Medoo([
		'type' => 'mssql',
		'host' => DATABASE_HOST,
		'port' => DATABASE_PORT,
		'database' => DATABASE_NAME,
		'username' => DATABASE_USERNAME,
		'password' => DATABASE_PASSWORD
	]);
} else {
	// 检查数据库是否存在，不存在则复制数据库
	if (!file_exists('../Data/Database.db3') || filesize('../Data/Database.db3') === 0) {
		if (!copy('../Binary/Database.sample.db3', '../Data/Database.db3')) {
			exit('数据库初始化失败，请检查 Data 目录是否拥有写入权限！');
		}
	}
	$database = new Medoo([
		'type' => 'sqlite',
		'database' => '../Data/Database.db3'
	]);
}
$helper = new GlobalHelper($database);

/**
 * 初始参数
 */
// 关闭 PHP 警告提示
if (DEBUG_MODE === false) {
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
}
// 检查更新
require_once '../Binary/Upgrade/Check.php';
// 获取控制器，并使用二进制安全的方式剥去字符串左右的空白与其中的 HTML 标签
$controller = !empty($_GET['c'])
	? htmlspecialchars(trim($_GET['c']))
	: (!empty($cron_mode)
		? 'Cron'
		: 'Index');
// 获取登录状态
$is_login = $helper->isLogin();

/**
 * 根据请求载入控制器
 */
// 对请求参数进行过滤，同时检查控制器文件是否存在
// 将 $controller 中的 '\' 替换为 '/'
$controller = str_replace('\\', '/', $controller);
$pattern = '%\./%';
if (preg_match_all($pattern, $controller)) {
	exit('非法请求！');
}

// 载入指定控制器
$controller_file = '../Controller/' . $controller . '.php';
if (file_exists($controller_file)) {
	require_once $controller_file;
	exit();
} else {
	// 找不到指定控制器
	require_once '../Controller/Index.php';
	exit();
}
