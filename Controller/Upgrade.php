<?php
/**
 * 更新控制器
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */

// 获取分页参数
$page = empty($_GET['page']) ? '' : htmlspecialchars(trim($_GET['page']));


/**
 * 全局鉴权
 */
if (!$is_login) {
	header('Location: ./index.php?c=Login');
	exit();
}


/**
 * 查询最新版本
 */
if ($page === 'CheckLatestVersion') {
	$curl_get_content = $helper->curlGet(CHECK_UPDATE_API_URL);
	if ($curl_get_content !== false && $curl_get_content !=='[]') {
		$curl_get_content = json_decode($curl_get_content, true);
		$version = substr($curl_get_content[0]['tag_name'], 1);
		$data = [
			'data' => $version
		];
		$helper->returnSuccess($data);
	} else {
		$helper->throwError(404, '无法获取最新版本！');
	}
}
