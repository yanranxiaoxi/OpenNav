<?php
/**
 * 选项配置控制器
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
 * 全局鉴权「Auth Safety」
 */
if (!$is_login) {
	$helper->throwError(403, '鉴权失败！');
}

/**
 * 设置站点选项
 */
if ($page === 'SetSite') {
	if (!empty($_POST['title']) && !empty($_POST['subtitle'])) {
		$logo = empty($_POST['logo']) ? '' : htmlspecialchars(trim($_POST['logo']));
		$keywords = empty($_POST['keywords']) ? '' : htmlspecialchars(trim($_POST['keywords']));
		$description = empty($_POST['description'])
			? ''
			: htmlspecialchars(trim($_POST['description']));
		$custom_header = empty($_POST['custom_header'])
			? ''
			: htmlspecialchars(trim($_POST['custom_header']));
		$custom_footer = empty($_POST['custom_footer'])
			? ''
			: htmlspecialchars(trim($_POST['custom_footer']));
		// 验证订阅状态
		if ($custom_footer !== '') {
			if (!$helper->isSubscribe()) {
				$helper->throwError(
					403,
					'未查询到授权信息，可能是与验证服务器之间的网络连接遇到问题，请稍后重试！'
				);
			}
		}
		$options_settings_site = [
			'title' => htmlspecialchars(trim($_POST['title'])),
			'logo' => $logo,
			'subtitle' => htmlspecialchars(trim($_POST['subtitle'])),
			'keywords' => $keywords,
			'description' => $description,
			'custom_header' => $custom_header,
			'custom_footer' => $custom_footer
		];
		$helper->setOptionsSettingsSite_AuthRequired($options_settings_site);
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 设置过渡页面选项
 */
if ($page === 'SetTransitionPage') {
	if (empty($_POST['control'])) {
		$_POST['control'] = 0;
	}
	if (isset($_POST['visitor_stay_time']) && isset($_POST['admin_stay_time'])) {
		$menu = empty($_POST['menu']) ? '' : htmlspecialchars(trim($_POST['menu']));
		$ad_top = empty($_POST['ad_top']) ? '' : htmlspecialchars(trim($_POST['ad_top']));
		$ad_bottom = empty($_POST['ad_bottom']) ? '' : htmlspecialchars(trim($_POST['ad_bottom']));
		// 验证订阅状态
		if ($menu !== '' || $ad_top !== '' || $ad_bottom !== '') {
			if (!$helper->isSubscribe()) {
				$helper->throwError(
					403,
					'未查询到授权信息，可能是与验证服务器之间的网络连接遇到问题，请稍后重试！'
				);
			}
		}
		$options_settings_transition_page = [
			'control' => intval($_POST['control']),
			'visitor_stay_time' => intval($_POST['visitor_stay_time']),
			'admin_stay_time' => intval($_POST['admin_stay_time']),
			'menu' => $menu,
			'ad_top' => $ad_top,
			'ad_bottom' => $ad_bottom
		];
		$helper->setOptionsSettingsTransitionPage_AuthRequired($options_settings_transition_page);
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 设置订阅选项
 */
if ($page === 'SetSubscribe') {
	if (!empty($_POST['domain']) && !empty($_POST['license_key']) && !empty($_POST['email'])) {
		// 此处逻辑来自 GlobalHelper->isSubscribe()
		$domain_array = explode(':', htmlspecialchars(trim($_POST['domain'])));
		// 将存入数据库中的选项数组
		$options_settings_subscribe = [
			'domain' => $domain_array[0],
			'license_key' => htmlspecialchars(trim($_POST['license_key'])),
			'email' => htmlspecialchars(trim($_POST['email']))
		];
		// 请求订阅查询接口返回数据
		$curl_subscribe_data = $helper->curlGet(
			API_URL . 'CheckSubscribe.php',
			$options_settings_subscribe,
			20
		);
		// 如果请求到了数据
		if ($curl_subscribe_data !== false) {
			// 解码请求到的数据
			$curl_subscribe_data = json_decode($curl_subscribe_data, true);
			if ($curl_subscribe_data['code'] === 200) {
				// 将请求到的 end_time 放入将存入数据库的数组
				$options_settings_subscribe['end_time'] = $curl_subscribe_data['data']['end_time'];
				// 将选项数组存入数据库中
				$helper->setOptionsSettingsSubscribe_AuthRequired($options_settings_subscribe);
				// 将返回给前端的时间戳格式化
				$curl_subscribe_data['data']['end_time'] = date(
					'Y-m-d',
					$curl_subscribe_data['data']['end_time']
				);
				// 将格式化后的时间返回给前端
				$data = [
					'data' => $curl_subscribe_data['data']
				];
				$helper->returnSuccess($data);
			} else {
				$helper->throwError(403, '订阅验证失败！');
			}
		} else {
			$helper->throwError(404, '接口请求失败，请重试！');
		}
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 删除订阅选项
 */
if ($page === 'DeleteSubscribe') {
	$options_settings_subscribe = [
		'domain' => '',
		'license_key' => '',
		'email' => '',
		'end_time' => 0
	];
	$helper->setOptionsSettingsSubscribe_AuthRequired($options_settings_subscribe);
	$helper->returnSuccess();
}
