<?php
/**
 * OpenNav 系统全局配置文件
 *
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 *
 * @link		https://opennav.soraharu.com/
 */

// 在线服务 API 地址
define('API_URL', 'https://opennav.soraharu.com/API/v1/');
// 检查更新 API 地址
define('CHECK_UPDATE_API_URL', 'https://gitlab.soraharu.com/api/v4/projects/90/releases');
// 用户代理（User-Agent）
define(
	'USER_AGENT',
	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36'
);
// 程序版本
define('VERSION', '0.1.5');

// 设置 PHP 超时时限
// ini_set('max_execution_time', 60);
set_time_limit(60);
// 设置 Contect-Type 请求头为 text/html
header('Content-Type: text/html; charset=utf-8');
