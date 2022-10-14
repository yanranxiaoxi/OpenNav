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

require_once __DIR__ . '/../Public/index.php';

/**
 * 检查主题
 */
// 获取主题选项
$options_theme = $helper->getOptionsTheme();
// 检查主题文件是否存在
if (
	!file_exists('./themes/' . $options_theme . '/index.php') ||
	!file_exists('./themes/' . $options_theme . '/opennav.info.json') ||
	!file_exists('./themes/' . $options_theme . '/opennav.config.json')
) {
	exit('主题 ' . $options_theme . ' 文件不完整，请尝试重新安装主题！');
}

/**
 * 获取分类与链接
 */
$parent_categories = $helper->getParentCategories();
$child_categories = [];
foreach ($parent_categories as $parent_category_value) {
	$parent_category_value_id = $parent_category_value['id'];
	$child_categories[$parent_category_value_id] = $helper->getChildCategoriesByParentCategoryId(
		$parent_category_value_id
	);
}
$categories = $helper->getCategories();
$links = [];
foreach ($categories as $category_value) {
	$category_value_id = $category_value['id'];
	$links[$category_value_id] = $helper->getLinksByCategoryId($category_value_id);
}

/**
 * 获取选项信息
 */
// 获取站点设置选项
$options_settings_site = $helper->getOptionsSettingsSite();
// 离线获取订阅状态
$options_settings_subscribe = $helper->getOptionsSettingsSubscribe_AuthRequired(); // unsafe
$status_subscribe = $options_settings_subscribe['end_time'] - time() > 0 ? true : false;
unset($options_settings_subscribe); // 保护订阅设置选项
// 获取主题信息
$theme_info = $helper->getThemeInfo($options_theme);
if (is_null($theme_info)) {
	exit('无法获取主题信息，可能是安装的主题存在问题。');
}
// 获取主题配置
$theme_config = $helper->getThemeConfig($options_theme);
if (is_null($theme_config)) {
	exit('无法获取主题信息，可能是安装的主题存在问题。');
}
// 获取暗色模式状态
$status_dark_mode = $helper->isDarkMode();

/**
 * 配置参数默认值
 */
$options_settings_site['title'] = empty($options_settings_site['title'])
	? 'OpenNav'
	: $options_settings_site['title'];
$options_settings_site['subtitle'] = empty($options_settings_site['subtitle'])
	? '开放、自由的个人网络收藏夹'
	: $options_settings_site['subtitle'];
// 主题根目录
$theme_directory_root = './themes/' . $options_theme . '/';

/**
 * 载入主题模板文件
 */
require_once './themes/' . $options_theme . '/index.php';

exit();
