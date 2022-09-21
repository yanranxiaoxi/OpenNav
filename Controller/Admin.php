<?php
/**
 * 管理页面控制器
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */


// 获取分页参数
$page = empty($_GET['page']) ? 'Index' : htmlspecialchars(trim($_GET['page']));


/**
 * 全局鉴权「Auth Safety」
 */
if (!$is_login) {
	header('Location: ./index.php?c=Login');
	exit();
}


/**
 * 进入首页流程
 */
if ($page === 'Index') {
	require_once('../Template/Admin/Index.php');
	exit();
}


/**
 * 进入分类列表流程
 */
if ($page === 'Categorys') {
	require_once('../Template/Admin/Categorys.php');
	exit();
}


/**
 * 进入编辑分类流程
 */
if ($page === 'EditCategory') {
	$category_id = intval($_GET['id']);
	$category_value = $helper->getCategoryByCategoryId_AuthRequired($category_id);
	if ($category_value['fid'] !== 0) {
		$category_value['ftitle'] = $helper->getCategoryTitleByCategoryId_AuthRequired($category_value['fid']);
	}
	$parent_categorys = $helper->getParentCategorysIdTitle_AuthRequired();
	require_once('../Template/Admin/EditCategory.php');
	exit();
}


/**
 * 进入添加分类流程
 */
if ($page === 'AddCategory') {
	$parent_categorys = $helper->getParentCategorysIdTitle_AuthRequired();
	require_once('../Template/Admin/AddCategory.php');
	exit();
}


/**
 * 进入链接列表流程
 */
if ($page === 'Links') {
	$categorys = $helper->getCategorysIdTitle_AuthRequired();
	require_once('../Template/Admin/Links.php');
	exit();
}


/**
 * 进入编辑链接流程
 */
if ($page === 'EditLink') {
	$link_id = intval($_GET['id']);
	$link_value = $helper->getLinkByLinkId_AuthRequired($link_id);
	$link_value['ftitle'] = $helper->getCategoryTitleByCategoryId_AuthRequired($link_value['fid']);
	$categorys = $helper->getCategorysIdTitle_AuthRequired();
	require_once('../Template/Admin/EditLink.php');
	exit();
}


/**
 * 进入添加链接流程
 */
if ($page === 'AddLink') {
	$categorys = $helper->getCategorysIdTitle_AuthRequired();
	require_once('../Template/Admin/AddLink.php');
	exit();
}


/**
 * 进入站点设置流程
 */
if ($page === 'Site') {
	$options_settings_site = $helper->getOptionsSettingsSite();
	require_once('../Template/Admin/Option/Site.php');
	exit();
}


/**
 * 进入过渡页面设置流程
 */
if ($page === 'TransitionPage') {
	$options_settings_transition_page = $helper->getOptionsSettingsTransitionPage();
	require_once('../Template/Admin/Option/TransitionPage.php');
	exit();
}


/**
 * 进入订阅设置流程
 */
if ($page === 'Subscribe') {
	$options_settings_subscribe = $helper->getOptionsSettingsSubscribe_AuthRequired();
	if ($options_settings_subscribe['end_time'] > time()) {
		$subscribe_end_time = date('Y-m-d', $options_settings_subscribe['end_time']);
	} elseif ($options_settings_subscribe['end_time'] === 1) {
		$subscribe_end_time = '永不到期';
	} else {
		$subscribe_end_time = '未授权';
	}
	require_once('../Template/Admin/Option/Subscribe.php');
	exit();
}


/**
 * 进入导入链接流程
 */
if ($page === 'ImportLinks') {
	require_once('../Template/Admin/ImportLinks.php');
	exit();
}
