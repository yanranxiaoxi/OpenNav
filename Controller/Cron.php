<?php
/**
 * 周期任务控制器
 *
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 *
 * @link		https://opennav.soraharu.com/
 */

require_once __DIR__ . '/../Public/index.php';

/**
 * 访问权限判定
 */
if (ALLOW_HTTP_CRON === true) {
	require_once '../Cron/Cron.php';
	exit();
} else {
	exit('非法访问请求！');
}

exit();
