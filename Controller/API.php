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
if ($page === 'Categorys') {
	$pages = intval($_GET['pages']);
	$limit = intval($_GET['limit']);
	if (!empty($pages) && !empty($limit)) {
		$categorys = $helper->getCategorysPagination_AuthRequired($pages, $limit);
		$data = [
			'count' => count($categorys),
			'data' => $categorys
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
		$data = [
			'count' => count($links),
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


/**
 * 上传书签文件
 */
if ($page === 'UploadLinksFile') {
	if ($_FILES['file']['error'] !== 0) {
		$helper->throwError(403, '文件上传失败！');
	} else {
		$file_name = $_FILES['file']['name'];
		// 获取文件后缀
		$file_suffix = explode('.', $file_name);
		$file_suffix = strtolower(end($file_suffix));
		// 临时文件位置
		$temp_file_directory = $_FILES['file']['tmp_name'];
		// 暂存文件位置
		$staging_file_name = $helper->getRandomKey(16) . '.' . $file_suffix;
		if ($file_suffix !== 'html' || filesize($temp_file_directory) > 1024 * 1024 * 8) {
			// 删除临时文件
			unlink($temp_file_directory);
			$helper->throwError(403, '不支持的文件类型或文件大小超过限制！');
		} elseif (copy($temp_file_directory, '../Cache/Upload/' . $staging_file_name)) {
			$data = [
				'data' => $staging_file_name
			];
			$helper->returnSuccess($data);
		} else {
			$helper->throwError(403, '文件暂存失败，可能是 /Cache/ 目录没有写入权限！');
		}
	}
}


/**
 * 导入书签文件
 */
if ($page === 'ImportLinks') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (empty($staging_file_name)) {
		$helper->throwError(403, '参数不完整！');
	}
	$staging_file_name = htmlspecialchars(trim($_POST['staging_file_name']));
	$property = intval($_POST['property']);
	if (!file_exists('../Cache/Upload/' . $staging_file_name)) {
		$helper->throwError(403, '文件不存在！');
	}
	// 解析 HTML 数据
	$staging_file_content = file_get_contents('../Cache/Upload/' . $staging_file_name);
	$staging_file_content_array = explode('\n', $staging_file_content); // 分割文本
	$links = []; // 链接组
	$categorys = []; // 分类组
	
}
