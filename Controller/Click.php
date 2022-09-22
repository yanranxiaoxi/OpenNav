<?php
/**
 * 链接跳转控制器
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
if (empty($_GET['id'])) {
	exit('非法访问请求！');
}
// 获取链接信息
$link = $helper->getLinkByLinkId(intval($_GET['id']));
// 判断是否可以查询到链接
if (empty($link)) {
	// 查询不到链接（链接不存在或访客未登录且链接为私有链接）
	exit('非法访问请求！可能是链接不存在或登录状态已过期。');
}
// 判断访客是否未登录（已登录就不存在私有链接，无需查询父分类）且是否查询不到上级分类（链接都拥有上级分类）
if (!$is_login) {
	$category = $helper->getCategoryFidPropertyByCategoryId_AuthRequired($link['fid']);
	if ($category['property'] === 1) {
		// 未登录且上级分类为私有分类
		exit('非法访问请求！可能是登录状态已过期。');
	}
	// 查询上级分类是否还有父分类，如有（上级分类的 fid 不为 0），则查询父分类的信息
	if ($category['fid'] !== 0) {
		$category_parent = $helper->getCategoryFidPropertyByCategoryId_AuthRequired($category['fid']);
		// 如果存在父分类且父分类为私有分类
		if ($category_parent['property'] === 1) {
			exit('非法访问请求！可能是登录状态已过期。');
		}
	}
}


/**
 * 进入跳转流程
 */
// 增加点击量
$helper->setLinkValueClick($link['id']);
// 判断是否已设置外部等待页（未登录且已设置的情况下跳转）
if (!$is_login && !empty($link['url_standby'])) {
	header('location: ' . $link['url_standby']);
	exit();
}
// 判断是否开启中转页
if ($options_settings_transition_page['control'] === 1 && ((!$is_login && $options_settings_transition_page['visitor_stay_time'] > 0) || ($is_login && $options_settings_transition_page['admin_stay_time'] > 0))) {
	// 获取站点设置选项
	$options_settings_site = $helper->getOptionsSettingsSite();
	// 获取过渡页设置选项
	$options_settings_transition_page = $helper->getOptionsSettingsTransitionPage();
	require_once('../Template/TransitionPage.php');
	exit();
}
// 直接跳转
header('location: ' . $link['url']);
