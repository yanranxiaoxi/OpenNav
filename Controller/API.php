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

// 设置返回格式为 Json
header('Content-Type: application/json; charset=utf-8');


/**
 * 全局鉴权「Auth Safety」
 */
if (!$helper->isLogin()) {
	$data = [
		'code' => 403,
		'message' => '鉴权失败！',
		'data' => ''
	];
	exit(json_encode($data));
}


/**
 * 获取分类列表
 * 
 * @todo #TODO# 增加排序方式参数
 */
if ($page === 'Categorys') {
	$pages = intval($_GET['pages']);
	$limit = intval($_GET['limit']);
	if (!empty($pages) && !empty($limit)) {
		$categorys = $helper->getCategorysPagination_AuthRequired($pages, $limit);
		$data = [
			'code' => 200,
			'message' => 'success',
			'count' => count($categorys),
			'data' => $categorys
		];
	} else {
		$data = [
			'code' => 403,
			'message' => '参数错误！',
			'data' => ''
		];
	}
	exit(json_encode($data));
}


/**
 * 删除分类
 */
if ($page === 'DeleteCategory') {
	if (!empty($_POST['id'])) {
		$category_id = intval($_POST['id']);
		$state = $helper->deleteCategory_AuthRequired($category_id);
		if ($state) {
			$data = [
				'code' => 200,
				'message' => 'success',
				'data' => true
			];
		} else {
			$data = [
				'code' => 403,
				'message' => '无法删除该分类，可能是该分类下拥有子分类或链接。',
				'data' => false
			];
		}
	} else {
		$data = [
			'code' => 403,
			'message' => '参数错误！',
			'data' => false
		];
	}
	exit(json_encode($data));
}


/**
 * 修改分类
 */
if ($page === 'EditCategory') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (isset($_POST['id']) && isset($_POST['fid']) && isset($_POST['weight']) && !empty($_POST['title']) && !empty($_POST['font_icon'])) {
		$category_id = intval($_POST['id']);
		$description = empty($_POST['description']) ? '' : htmlspecialchars(trim($_POST['description']));
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
			$data = [
				'code' => 200,
				'message' => 'success'
			];
		} else {
			$data = [
				'code' => 403,
				'message' => $state
			];
		}
	} else {
		$data = [
			'code' => 403,
			'message' => '参数错误！'
		];
	}
	exit(json_encode($data));
}


/**
 * 添加分类
 */
if ($page === 'AddCategory') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (isset($_POST['fid']) && isset($_POST['weight']) && !empty($_POST['title']) && !empty($_POST['font_icon'])) {
		$description = empty($_POST['description']) ? '' : htmlspecialchars(trim($_POST['description']));
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
			$data = [
				'code' => 200,
				'message' => 'success'
			];
		} else {
			$data = [
				'code' => 403,
				'message' => $state
			];
		}
	} else {
		$data = [
			'code' => 403,
			'message' => '参数错误！'
		];
	}
	exit(json_encode($data));
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
		$data = [
			'code' => 200,
			'message' => 'success',
			'count' => count($links),
			'data' => $links
		];
	} else {
		$data = [
			'code' => 403,
			'message' => '参数错误！',
			'data' => ''
		];
	}
	exit(json_encode($data));
}


/**
 * 删除链接
 */
if ($page === 'DeleteLink') {
	if (!empty($_POST['id'])) {
		$link_id = intval($_POST['id']);
		$helper->deleteLink_AuthRequired($link_id);
		$data = [
			'code' => 200,
			'message' => 'success',
			'data' => true
		];
	} else {
		$data = [
			'code' => 403,
			'message' => '参数错误！',
			'data' => false
		];
	}
	exit(json_encode($data));
}


/**
 * 修改链接
 */
if ($page === 'EditLink') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (isset($_POST['id']) && isset($_POST['fid']) && isset($_POST['weight']) && !empty($_POST['title']) && !empty($_POST['url'])) {
		$link_id = intval($_POST['id']);
		$description = empty($_POST['description']) ? '' : htmlspecialchars(trim($_POST['description']));
		$url_standby = empty($_POST['url_standby']) ? '' : htmlspecialchars(trim($_POST['url_standby']));
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
			$data = [
				'code' => 200,
				'message' => 'success'
			];
		} else {
			$data = [
				'code' => 403,
				'message' => $state
			];
		}
	} else {
		$data = [
			'code' => 403,
			'message' => '参数错误！'
		];
	}
	exit(json_encode($data));
}


/**
 * 添加链接
 */
if ($page === 'AddLink') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (isset($_POST['fid']) && isset($_POST['weight']) && !empty($_POST['title']) && !empty($_POST['url'])) {
		$description = empty($_POST['description']) ? '' : htmlspecialchars(trim($_POST['description']));
		$url_standby = empty($_POST['url_standby']) ? '' : htmlspecialchars(trim($_POST['url_standby']));
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
			$data = [
				'code' => 200,
				'message' => 'success'
			];
		} else {
			$data = [
				'code' => 403,
				'message' => $state
			];
		}
	} else {
		$data = [
			'code' => 403,
			'message' => '参数错误！'
		];
	}
	exit(json_encode($data));
}


/**
 * 获取链接描述信息
 */
if ($page === 'GetLinkInfo') {
	// URL 为空
	if (empty($_POST['url'])) {
		$data = [
			'code' => 403,
			'message' => 'URL 不能为空！',
			'data' => ''
		];
		exit(json_encode($data));
	}
	// 使用正则检测 URL 是否合法
	$url_regex = '/^(https?:\/\/)[\S]+$/';
	if (!preg_match($url_regex, $_POST['url'])) {
		$data = [
			'code' => 403,
			'message' => 'URL 格式不正确！',
			'data' => ''
		];
		exit(json_encode($data));
	}
	$meta_tags = get_meta_tags($_POST['url']);
	if ($meta_tags !== false) {
		$data = [
			'code' => 200,
			'message' => 'success',
			'data' => empty($meta_tags['description']) ? '' : $meta_tags['description']
		];
	} else {
		$data = [
			'code' => 404,
			'message' => '描述信息获取失败！',
			'data' => ''
		];
	}
	exit(json_encode($data));
}
