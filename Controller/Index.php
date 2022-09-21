<?php
/**
 * 首页控制器
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */


/**
 * 获取分类与链接
 */
$parent_categorys = $helper->getParentCategorys();
$child_categorys = [];
foreach ($parent_categorys as $parent_category_value) {
	$parent_category_value_id = $parent_category_value['id'];
	$child_categorys[$parent_category_value_id] = $helper->getChildCategorysByParentCategoryId($parent_category_value_id);
}
$categorys = $helper->getCategorys();
$links = [];
foreach ($categorys as $category_value) {
	$category_value_id = $category_value['id'];
	$links[$category_value_id] = $helper->getLinksByCategoryId($category_value_id);
}


/**
 * 获取选项信息
 */
// 获取主题选项
$options_theme = $helper->getOptionsTheme();
// 获取站点设置选项
$options_settings_site = $helper->getOptionsSettingsSite();
// 获取主题信息
$theme_info = $helper->getThemeInfo($options_theme);
if (is_null($theme_info)) {
	exit('无法获取主题信息，可能是主题文件夹内的 info.json 没有读取权限或安装的主题存在问题。');
}
// 获取主题配置
$theme_config = $helper->getThemeConfig($options_theme);
if (is_null($theme_config)) {
	exit('无法获取主题信息，可能是主题文件夹内的 config.json 没有读取权限或安装的主题存在问题。');
}
// 设置暗色模式
$theme_layout = $helper->isDarkMode() ? 'mdui-theme-layout-dark' : '';


/**
 * 配置参数默认值
 */
$options_settings_site['title'] = empty($options_settings_site['title']) ? 'OpenNav' : $options_settings_site['title'];
$options_settings_site['subtitle'] = empty($options_settings_site['subtitle']) ? '开放、自由的个人网络收藏夹' : $options_settings_site['subtitle'];


/**
 * 载入主题模板文件
 */
require_once('./themes/' . $options_theme . '/index.php');
