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

require_once __DIR__ . '/../Public/index.php';

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
	if ($curl_get_content !== false && $curl_get_content !== '[]') {
		$curl_get_content = json_decode($curl_get_content, true);
		$version = substr($curl_get_content[0]['tag_name'], 1);
		$download_link = $curl_get_content[0]['assets']['links'][0]['direct_asset_url'];
		$released_at = $curl_get_content[0]['released_at'];
		$download_cache = file_exists('../Cache/Upgrade/' . $version . '.zip');
		$data['data'] = [
			'version' => $version,
			'released_at' => $released_at,
			'download_link' => $download_link,
			'download_cache' => $download_cache
		];
		$helper->returnSuccess($data);
	} else {
		$helper->throwError(404, '无法获取最新版本！');
	}
}

/**
 * 获取最新版本软件包
 */
if ($page === 'GetPackage') {
	set_time_limit(180);
	if (!$helper->isSubscribe()) {
		$helper->throwError(
			403,
			'未查询到授权信息，可能是与验证服务器之间的网络连接遇到问题或未绑定授权，请稍后重试！'
		);
	}
	unlink('../Cache/Upgrade/' . $_POST['latest_version'] . '.zip.downloading');
	unlink('../Cache/Upgrade/' . $_POST['latest_version'] . '.zip');
	$download_status = $helper->curlDownload_AuthRequired(
		$_POST['download_link'],
		'../Cache/Upgrade/' . $_POST['latest_version'] . '.zip.downloading',
		165
	);
	if ($download_status) {
		if (
			rename(
				'../Cache/Upgrade/' . $_POST['latest_version'] . '.zip.downloading',
				'../Cache/Upgrade/' . $_POST['latest_version'] . '.zip'
			)
		) {
			$data['data'] = [
				'version' => $_POST['latest_version']
			];
			$helper->returnSuccess($data);
		} else {
			$helper->throwError(403, '下载完成但重命名失败，请检查 /Cache/ 目录权限！');
		}
	} else {
		$helper->throwError(404, '无法下载软件包，请检查网络状态或 /Cache/ 目录权限！');
	}
}

exit();
