<?php
/**
 * 网站图标控制器
 * 进入 Online 流程需要 fileinfo 支持
 * 进入 Offline 流程需要 mbstring 支持
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

// 获取分页参数
$page = empty($_GET['page']) ? '' : htmlspecialchars(trim($_GET['page']));


/**
 * 在线获取 Favicon
 */
if ($page === 'Online') {
	// 获取当前主机名
	$http_host = $_SERVER['HTTP_HOST'];
	// 获取 Referer
	$http_referer = $_SERVER['HTTP_REFERER'];
	// 如果 Referer 和主机名不匹配，则仅返回默认图标
	if ((!empty($referer)) && (!strstr($http_host, $http_referer))) {
		$icon = file_get_contents('./assets/images/default-favicon.ico', 'rb');
		header('Content-Type: image/x-icon; charset=utf-8');
		exit($icon);
	}
	// URL 为空
	if (empty($_GET['url'])) {
		exit('缺少参数！');
	}
	// 使用正则检测 URL 是否合法
	$url_regex = '/^(https?:\/\/)[\S]+$/';
	if (!preg_match($url_regex, $_GET['url'])) {
		$icon = file_get_contents('./assets/images/default-favicon.ico', 'rb');
		header('Cache-Control: max-age=604800');
		header('Content-Type: image/x-icon; charset=utf-8');
		exit($icon);
	}

	$settings_favicon = [
		// 缓存目录
		'dir' => '../Cache/',
		// 缓存有效期，单位：秒
		'timeout' => 2592000,
		// 默认图片
		'defaultico' => './assets/images/default-favicon.ico'
	];
	$favicon->cache($settings_favicon);
	$image_file_name = $favicon->get($_GET['url'], FaviconDLType::DL_FILE_PATH);
	// 未获取到 Favicon 图像
	if ($image_file_name === false) {
		$icon = file_get_contents('./assets/images/default-favicon.ico', 'rb');
		header('Cache-Control: max-age=604800');
		header('Content-Type: image/x-icon; charset=utf-8');
		exit($icon);
	}
	$url_file_name = 'url' . substr($image_file_name, 3);
	$image_url = file_exists('../Cache/' . $url_file_name) ? file_get_contents('../Cache/' . $url_file_name, 'rb') : exit('读取缓存失败！');
	$image_url = explode(".", $image_url);
	$image_ext = end($image_url);
	$icon = file_exists('../Cache/' . $image_file_name) ? file_get_contents('../Cache/' . $image_file_name, 'rb') : exit('读取缓存失败！');
	switch ($image_ext) {
		case 'jpg':
		case 'jpeg':
			header('Content-Type: image/jpeg; charset=utf-8');
			break;
		case 'png':
			header('Content-Type: image/png; charset=utf-8');
			break;
		case 'ico':
			header('Content-Type: image/x-icon; charset=utf-8');
			break;
		case 'gif':
			header('Content-Type: image/gif; charset=utf-8');
			break;
		case 'bmp':
			header('Content-Type: image/bmp; charset=utf-8');
			break;
		case 'webp':
			header('Content-Type: image/webp; charset=utf-8');
			break;
		case 'svg':
			header('Content-Type: image/svg+xml; charset=utf-8');
			break;
		default:
			break;
	}
	header('Cache-Control: max-age=604800');
	exit($icon);
}


/**
 * 离线生成标题 Favicon
 */
if ($page === 'Offline') {
	// 获取当前主机名
	$http_host = $_SERVER['HTTP_HOST'];
	// 获取 Referer
	$http_referer = $_SERVER['HTTP_REFERER'];
	// 如果 Referer 和主机名不匹配，则仅返回默认图标
	if ((!empty($referer)) && (!strstr($http_host, $http_referer))) {
		$icon = file_get_contents('./assets/images/default-favicon.ico', 'rb');
		header('Content-Type: image/x-icon; charset=utf-8');
		exit($icon);
	}

	$title = empty($_GET['title']) ? '空' : htmlspecialchars(trim($_GET['title']));
	$total = unpack('L', hash('adler32', $title, true))[1];
	$hue = $total % 360;
	list($r, $g, $b) = $helper->hsvToRgb($hue / 360, 0.3, 0.9);

	$bg = 'rgb({$r}, {$g}, {$b})';
	$color = '#ffffff';
	$first = mb_strtoupper(mb_substr($title, 0, 1));
	$icon = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="100" width="100"><rect fill="' . $bg . '" x="0" y="0" width="100" height="100"></rect><text x="50" y="50" font-size="50" text-copy="fast" fill="' . $color . '" text-anchor="middle" text-rights="admin" alignment-baseline="central">' . $first . '</text></svg>';
	header('Cache-Control: max-age=604800');
	header('Content-Type: image/svg+xml; charset=utf-8');
	exit($icon);
}
