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
	if (!empty($_POST['id'])) {
		$category_id = intval($_POST['id']);
		$state = $helper->deleteCategory_AuthRequired($category_id);
		if ($state) {
			$helper->returnSuccess();
		} else {
			$helper->throwError(403, '无法删除该分类，可能是该分类下拥有子分类或链接。');
		}
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 修改分类
 */
if ($page === 'EditCategory') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (
		isset($_POST['id']) &&
		isset($_POST['fid']) &&
		isset($_POST['weight']) &&
		!empty($_POST['title']) &&
		!empty($_POST['font_icon'])
	) {
		$category_id = intval($_POST['id']);
		$description = empty($_POST['description'])
			? ''
			: htmlspecialchars(trim($_POST['description']));
		$category_data = [
			'fid' => intval($_POST['fid']),
			'weight' => intval($_POST['weight']),
			'title' => htmlspecialchars(trim($_POST['title'])),
			'font_icon' => htmlspecialchars(trim($_POST['font_icon'])),
			'description' => $description,
			'property' => intval($_POST['property'])
		];
		$state = $helper->updateCategory_AuthRequired($category_id, $category_data);
		if ($state === true) {
			$helper->returnSuccess();
		} else {
			$helper->throwError(403, '分类修改失败！');
		}
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 添加分类
 */
if ($page === 'AddCategory') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (
		isset($_POST['fid']) &&
		isset($_POST['weight']) &&
		!empty($_POST['title']) &&
		!empty($_POST['font_icon'])
	) {
		$description = empty($_POST['description'])
			? ''
			: htmlspecialchars(trim($_POST['description']));
		$category_data = [
			'fid' => intval($_POST['fid']),
			'weight' => intval($_POST['weight']),
			'title' => htmlspecialchars(trim($_POST['title'])),
			'font_icon' => htmlspecialchars(trim($_POST['font_icon'])),
			'description' => $description,
			'property' => intval($_POST['property'])
		];
		$state = $helper->addCategory_AuthRequired($category_data);
		if ($state === true) {
			$helper->returnSuccess();
		} else {
			$helper->throwError(403, '分类添加失败！');
		}
	} else {
		$helper->throwError(403, '参数错误！');
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
	if (!empty($_POST['id'])) {
		$link_id = intval($_POST['id']);
		$helper->deleteLink_AuthRequired($link_id);
		$helper->returnSuccess();
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 修改链接
 */
if ($page === 'EditLink') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (
		isset($_POST['id']) &&
		isset($_POST['fid']) &&
		isset($_POST['weight']) &&
		!empty($_POST['title']) &&
		!empty($_POST['url'])
	) {
		$link_id = intval($_POST['id']);
		$description = empty($_POST['description'])
			? ''
			: htmlspecialchars(trim($_POST['description']));
		$url_standby = empty($_POST['url_standby'])
			? ''
			: htmlspecialchars(trim($_POST['url_standby']));
		$link_data = [
			'fid' => intval($_POST['fid']),
			'weight' => intval($_POST['weight']),
			'title' => htmlspecialchars(trim($_POST['title'])),
			'url' => htmlspecialchars(trim($_POST['url'])),
			'url_standby' => $url_standby,
			'description' => $description,
			'property' => intval($_POST['property'])
		];
		$state = $helper->updateLink_AuthRequired($link_id, $link_data);
		if ($state === true) {
			$helper->returnSuccess();
		} else {
			$helper->throwError(403, '链接修改失败！');
		}
	} else {
		$helper->throwError(403, '参数错误！');
	}
}

/**
 * 添加链接
 */
if ($page === 'AddLink') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (
		isset($_POST['fid']) &&
		isset($_POST['weight']) &&
		!empty($_POST['title']) &&
		!empty($_POST['url'])
	) {
		$description = empty($_POST['description'])
			? ''
			: htmlspecialchars(trim($_POST['description']));
		$url_standby = empty($_POST['url_standby'])
			? ''
			: htmlspecialchars(trim($_POST['url_standby']));
		$link_data = [
			'fid' => intval($_POST['fid']),
			'weight' => intval($_POST['weight']),
			'title' => htmlspecialchars(trim($_POST['title'])),
			'url' => htmlspecialchars(trim($_POST['url'])),
			'url_standby' => $url_standby,
			'description' => $description,
			'property' => intval($_POST['property'])
		];
		$state = $helper->addLink_AuthRequired($link_data);
		if ($state === true) {
			$helper->returnSuccess();
		} else {
			$helper->throwError(403, '链接添加失败！');
		}
	} else {
		$helper->throwError(403, '参数错误！');
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
	// 使用正则检测 URL 是否合法
	$url_regex = '/^(https?:\/\/)[\S]+$/';
	if (!preg_match($url_regex, $_POST['url'])) {
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
