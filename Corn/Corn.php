<?php
/**
 * 周期任务组
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */

use Favicon\Favicon;
use Favicon\FaviconDLType;

$favicon = new Favicon();

// 设置周期任务运行时限
ini_set('max_execution_time', 7200); // 2 hour

// 新建日志文件
$timestamp = time();
$log_file = fopen('../Cache/Log/Corn-' . $timestamp . '.txt', 'w');

// 获取链接图标
$settings_favicon = [
	// 缓存目录
	'dir' => '../Cache/Favicon/',
	// 缓存有效期，单位：秒
	'timeout' => 2592000
];
$favicon->cache($settings_favicon);
$links_url = $helper->getLinksUrl();
foreach ($links_url as $link_value_url) {
	$url_regex = '/^(https?:\/\/)[\S]+$/';
	if (preg_match($url_regex, $link_value_url)) {
		$favion_status = $favicon->get($link_value_url, FaviconDLType::DL_FILE_PATH);
		if ($favion_status !== false) {
			$log_status_string = 'INFO';
			$favion_lore_string = 'successfully.';
		} else {
			$log_status_string = 'WARNING';
			$favion_lore_string = 'failed.';
		}
	} else {
		$log_status_string = 'INFO';
		$favion_lore_string = 'skipped, not a valid website url.';
	}
	$log_string = '[' . date('Y-m-d H:i', time()) . '] ' . $log_status_string . ': Get favicon => (' . $link_value_url . ') ' . $favion_lore_string;
	fwrite($log_file, $log_string);
}

// 关闭日志文件
fclose($log_file);
