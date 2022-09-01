<?php
/**
 * 自动更新控制器
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
if (!$helper->isLogin()) {
	header('Location: ./index.php?c=Login');
	exit();
}


/**
 * 查询最新版本
 */
if ($page === 'CheckLatestVersion') {
	$curl_get_content = $helper->curlGet('https://gitlab.soraharu.com/api/v4/projects/90/releases');
	if ($curl_get_content !== false && $curl_get_content !=='[]') {
		$curl_get_content = json_decode($curl_get_content, true);
		$data = [
			'code' => 200,
			'message' => 'success',
			'data' => $curl_get_content[0]['name']
		];
	} else {
		$data = [
			'code' => 404,
			'message' => '无法获取最新版本',
			'data' => '0.0.0'
		];
	}
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($data));
}
