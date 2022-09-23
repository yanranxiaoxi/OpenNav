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


/**
 * 重复执行检查
 */
$timestamp = time();
$last_timestamp = intval(file_get_contents('../Cache/Log/.cron.timestamp'));
if ($timestamp - $last_timestamp < 7150) { // 2 hour - 50 second 防止计时误差
	exit('周期任务间隔时间最短为 2 小时！');
}


/**
 * 任务初始化
 */
// 设置周期任务运行时限
ini_set('max_execution_time', 7000); // 2 hour - 200 second

// 保存执行时间戳
file_put_contents('../Cache/Log/.cron.timestamp', strval($timestamp));

// 新建日志文件
$log_file = fopen('../Cache/Log/' . $timestamp . '.cron.log', 'a');


/**
 * 获取链接图标
 * 仅主题存在 online_favicon 配置项且为 true 时执行
 */
// 获取主题选项
$options_theme = $helper->getOptionsTheme();
// 获取主题配置
$theme_config = $helper->getThemeConfig($options_theme);
if (isset($theme_config['online_favicon'])) {
	if($theme_config['online_favicon'] === true) {
		// 初始化 Favicon 类
		$favicon = new Favicon();
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
			$log_string = '[' . date('Y-m-d H:i', time()) . '] ' . $log_status_string . ': Get favicon => (' . $link_value_url . ') ' . $favion_lore_string . "\n";
			fwrite($log_file, $log_string);
		}
	}
}


/**
 * 任务收尾
 */
// 关闭日志文件
fclose($log_file);
