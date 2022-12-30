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

namespace OpenNav\Cron;

// 直接 Cron 模式
$cron_mode = true;

require_once __DIR__ . '/../Public/index.php';
require_once __DIR__ . '/../Controller/Cron.php';

use Favicon\Favicon;
use Favicon\FaviconDLType;

/**
 * 重复执行检查
 */
$timestamp = time();
$last_timestamp = file_exists('../Cache/Log/.cron.timestamp')
	? intval(file_get_contents('../Cache/Log/.cron.timestamp'))
	: 0;
if ($timestamp - $last_timestamp < 7150) {
	// 2 hours - 50 seconds 防止计时误差
	exit('周期任务间隔时间最短为 2 小时！');
}

/**
 * 任务初始化
 */
// 设置周期任务运行时限
set_time_limit(7000); // 2 hours - 200 seconds
// 保存执行时间戳
file_put_contents('../Cache/Log/.cron.timestamp', strval($timestamp));
// 新建日志文件
$log_file = fopen('../Cache/Log/' . $timestamp . '.cron.log', 'a');
// 移除前次 Error 信息文件
if (file_exists('../Cache/Log/.cron.error')) {
	unlink('../Cache/Log/.cron.error');
}
// 创建 Error 信息文件
function cronError(): void {
	file_put_contents('../Cache/Log/.cron.error', '');
}

/**
 * 清理 Cache 目录
 */
// 可排序缓存
$caches = [
	'Log' => [
		'/^[0-9]+\.cron\.log$/' => 604800 // 7 days
	],
	'Upload' => [
		'/^[0-9]+\.links\.(html|xlsx|csv)$/' => 7200 // 2 hours
	]
];
foreach ($caches as $directory_name => $cache_info) {
	foreach ($cache_info as $file_name_regex => $file_expiration_time) {
		$file_names = scandir('../Cache/' . $directory_name . '/');
		foreach ($file_names as $file_name) {
			if (preg_match($file_name_regex, $file_name)) {
				if (
					$timestamp - filectime('../Cache/' . $directory_name . '/' . $file_name) >=
					$file_expiration_time
				) {
					if (unlink('../Cache/' . $directory_name . '/' . $file_name)) {
						$log_status_string = 'INFO';
						$cache_lore_string = 'has expired. Automatic deletion succeeded.';
					} else {
						cronError();
						$log_status_string = 'ERROR';
						$cache_lore_string =
							'has expired. However, an error occurred while deleting the file.';
					}
					$log_string =
						'[' .
						date('Y-m-d H:i') .
						'] ' .
						$log_status_string .
						': Check expired status => (/Cache/' .
						$directory_name .
						'/' .
						$file_name .
						') ' .
						$cache_lore_string .
						"\n";
					fwrite($log_file, $log_string);
				} else {
					$log_string =
						'[' .
						date('Y-m-d H:i') .
						'] INFO: Check expired status => (/Cache/' .
						$directory_name .
						'/' .
						$file_name .
						') has not expired and all files of the same type have been skipped after that.' .
						"\n";
					fwrite($log_file, $log_string);
					break;
				}
			}
		}
	}
}

// 全随机缓存
$caches = [
	'Favicon' => [
		'/^[0-9a-z]{20}\.offline\.svg$/' => 15552000 // 180 days
	],
	'Upgrade' => [
		'/^\d{1,}\.\d{1,}\.\d{1,}\.zip$/' => 604800 // 7 days
	]
];
foreach ($caches as $directory_name => $cache_info) {
	foreach ($cache_info as $file_name_regex => $file_expiration_time) {
		$file_names = scandir('../Cache/' . $directory_name . '/');
		foreach ($file_names as $file_name) {
			if (preg_match($file_name_regex, $file_name)) {
				if (
					$timestamp - filectime('../Cache/' . $directory_name . '/' . $file_name) >=
					$file_expiration_time
				) {
					if (unlink('../Cache/' . $directory_name . '/' . $file_name)) {
						$log_status_string = 'INFO';
						$cache_lore_string = 'has expired. Automatic deletion succeeded.';
					} else {
						cronError();
						$log_status_string = 'ERROR';
						$cache_lore_string =
							'has expired. However, an error occurred while deleting this file.';
					}
				} else {
					$log_status_string = 'INFO';
					$cache_lore_string = 'has not expired.';
				}
				$log_string =
					'[' .
					date('Y-m-d H:i') .
					'] ' .
					$log_status_string .
					': Check expired status => (/Cache/' .
					$directory_name .
					'/' .
					$file_name .
					') ' .
					$cache_lore_string .
					"\n";
				fwrite($log_file, $log_string);
			}
		}
	}
}

/**
 * 获取链接图标
 * 仅主题存在 online_favicon 配置项且为 true 时执行
 */
// 获取主题选项
$options_theme = $helper->getOptionsTheme();
// 获取主题配置
$theme_config = $helper->getThemeConfig($options_theme);
if (isset($theme_config['online_favicon'])) {
	if ($theme_config['online_favicon'] === true) {
		// 初始化 Favicon 类
		$favicon = new Favicon();
		$settings_favicon = [
			// 缓存目录
			'dir' => '../Cache/Favicon/',
			// 缓存有效期，单位：秒
			'timeout' => 2592000 // 30 days
		];
		$favicon->cache($settings_favicon);
		$links_url = $helper->getLinksUrl_AuthRequired(); // 此处不会有任何前端返回，该数据安全
		foreach ($links_url as $link_value_url) {
			if ($helper->validateUrl($link_value_url)) {
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
			$log_string =
				'[' .
				date('Y-m-d H:i') .
				'] ' .
				$log_status_string .
				': Get favicon => (' .
				$link_value_url .
				') ' .
				$favion_lore_string .
				"\n";
			fwrite($log_file, $log_string);
		}
	}
}

/**
 * 更新 PublicSuffixList
 */
$public_suffix_list_data_url = 'https://publicsuffix.org/list/public_suffix_list.dat';
$public_suffix_list_data = $helper->curlGet($public_suffix_list_data_url, null, 300);
if ($public_suffix_list_data) {
	if (file_put_contents('../Data/PublicSuffixList.dat', $public_suffix_list_data)) {
		fwrite(
			$log_file,
			'[' .
				date('Y-m-d H:i') .
				'] ' .
				'INFO: Download => (' .
				$public_suffix_list_data_url .
				') OK. PublicSuffixList has been updated!'
		);
	} else {
		fwrite(
			$log_file,
			'[' .
				date('Y-m-d H:i') .
				'] ' .
				'ERROR: Download => (' .
				$public_suffix_list_data_url .
				') OK. But PublicSuffixList cannot be updated!'
		);
	}
} else {
	fwrite(
		$log_file,
		'[' .
			date('Y-m-d H:i') .
			'] ' .
			'ERROR: Download => (' .
			$public_suffix_list_data_url .
			') failure. Could not update PublicSuffixList.'
	);
}

/**
 * 任务收尾
 */
// 关闭日志文件
fclose($log_file);
// 修改日志文件为只读
// chmod('../Cache/Log/' . $timestamp . '.cron.log', 0444);

exit();
