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


/**
 * 访问权限判定
 */
if (ALLOW_HTTP_CORN === true) {
	require_once('../Corn/Corn.php');
	exit();
} else {
	exit('非法访问请求！');
}
