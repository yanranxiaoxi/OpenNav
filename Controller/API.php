<?php
/**
 * 管理员 API 控制器
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
 * 全局鉴权「Auth Safety」
 */
if (!$is_login) {
	$helper->throwError(403, '鉴权失败！');
}

/**
 * 获取分类列表
 *
 * @todo #TODO# 增加排序方式参数
 */
if ($page === 'Categories') {
	$pages = intval($_GET['pages']);
	$limit = intval($_GET['limit']);
	if (!empty($pages) && !empty($limit)) {
		$categories = $helper->getCategoriesPagination_AuthRequired($pages, $limit);
		// fid 数组
		$fid_array = [];
		foreach ($categories as $category) {
			if ($category['fid'] !== 0) {
				array_push($fid_array, $category['fid']);
			}
		}
		$fid_array = array_unique($fid_array); // 去重
		$fid_array = array_values($fid_array); // 排序
		// ftitle 数组
		$ftitle_array = $helper->getCategoriesTitleByCategoriesId_AuthRequired($fid_array);
		// fid 为 key，ftitle 为 value 数组
		$fidtitle_array = [];
		for ($i = 0; $i < count($fid_array); $i++) {
			$fid = strval($fid_array[$i]);
			$fidtitle_array[$fid] = $ftitle_array[$i];
		}
		// 将 ftitle 插入 $categories 数组中
		foreach ($categories as $key => $category) {
			if ($categories[$key]['fid'] !== 0) {
				$fid = strval($category['fid']);
				$categories[$key]['ftitle'] = $fidtitle_array[$fid];
			}
		}
		// 输出
		$data = [
			'count' => $helper->countCategories_AuthRequired(),
			'data' => $categories
		];
		$helper->returnSuccess($data);
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 删除分类
 */
if ($page === 'DeleteCategory') {
	if (empty($_POST['id'])) {
		$helper->throwError(403, '未指定分类 ID');
	}
	$state = $helper->deleteCategory_AuthRequired(intval($_POST['id']));
	if ($state) {
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, '无法删除该分类，可能是该分类下拥有子分类或链接。');
	}
}

/**
 * 修改分类
 */
if ($page === 'EditCategory') {
	if (empty($_POST['id'])) {
		$helper->throwError(403, '未指定分类 ID');
	}
	$state = $helper->updateCategory_AuthRequired(intval($_POST['id']), $_POST);
	if ($state === true) {
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, $state);
	}
}

/**
 * 添加分类
 */
if ($page === 'AddCategory') {
	$state = $helper->addCategory_AuthRequired($_POST);
	if ($state === true) {
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, $state);
	}
}

/**
 * 获取链接列表
 *
 * @todo #TODO# 增加排序方式参数
 */
if ($page === 'Links') {
	$pages = intval($_GET['pages']);
	$limit = intval($_GET['limit']);
	if (!empty($pages) && !empty($limit)) {
		$links = $helper->getLinksPagination_AuthRequired($pages, $limit);
		// fid 数组
		$fid_array = [];
		foreach ($links as $link) {
			array_push($fid_array, $link['fid']);
		}
		$fid_array = array_unique($fid_array); // 去重
		$fid_array = array_values($fid_array); // 排序
		// ftitle 数组
		$ftitle_array = $helper->getCategoriesTitleByCategoriesId_AuthRequired($fid_array);
		// fid 为 key，ftitle 为 value 数组
		$fidtitle_array = [];
		for ($i = 0; $i < count($fid_array); $i++) {
			$fid = strval($fid_array[$i]);
			$fidtitle_array[$fid] = $ftitle_array[$i];
		}
		// 将 ftitle 插入 $links 数组中
		foreach ($links as $key => $link) {
			$fid = strval($link['fid']);
			$links[$key]['ftitle'] = $fidtitle_array[$fid];
		}
		// 输出
		$data = [
			'count' => $helper->countLinks_AuthRequired(),
			'data' => $links
		];
		$helper->returnSuccess($data);
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 删除链接
 */
if ($page === 'DeleteLink') {
	if (empty($_POST['id'])) {
		$helper->throwError(403, '未指定链接 ID');
	}
	$helper->deleteLink_AuthRequired(intval($_POST['id']));
	$helper->returnSuccess();
}

/**
 * 修改链接
 */
if ($page === 'EditLink') {
	if (empty($_POST['id'])) {
		$helper->throwError(403, '未指定链接 ID');
	}
	$state = $helper->updateLink_AuthRequired(intval($_POST['id']), $_POST);
	if ($state === true) {
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, $state);
	}
}

/**
 * 添加链接
 */
if ($page === 'AddLink') {
	$state = $helper->addLink_AuthRequired($_POST);
	if ($state === true) {
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, $state);
	}
}

/**
 * 获取链接描述信息
 */
if ($page === 'GetLinkInfo') {
	// URL 为空
	if (empty($_POST['url'])) {
		$helper->throwError(403, 'URL 不能为空！');
	}
	// 检测 URL 是否合法
	if (!$helper->validateUrl($_POST['url'])) {
		$helper->throwError(403, 'URL 格式不正确！');
	}
	$meta_tags = get_meta_tags($_POST['url']);
	if ($meta_tags !== false) {
		$data = [
			'data' => empty($meta_tags['description']) ? '' : $meta_tags['description']
		];
		$helper->returnSuccess($data);
	} else {
		$helper->throwError(404, '描述信息获取失败！');
	}
}

exit();
