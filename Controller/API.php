<?php
/**
 * 管理员 API 控制器
 * 进入 ImportLinks 流程需要 mbstring 支持
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
		$staging_file_name = time() . '.links.html';
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
	if (empty($_POST['staging_file_name'])) {
		$helper->throwError(403, '参数不完整！');
	}
	$staging_file_name = htmlspecialchars(trim($_POST['staging_file_name']));
	$property = intval($_POST['property']);
	if (!file_exists('../Cache/Upload/' . $staging_file_name)) {
		$helper->throwError(403, '文件不存在！');
	}

	// 设置导入任务运行时限
	ini_set('max_execution_time', 300); // 5 minutes
	// 解析 HTML 数据
	$staging_file_content = file_get_contents('../Cache/Upload/' . $staging_file_name);
	$staging_file_content_array = explode("\n", $staging_file_content); // 分割文本
	$links = []; // 链接组
	$categorys = []; // 分类组
	$default_category_id = 0; // 默认分类 ID
	$latest_addition = 0; // 0: link, 1: category
	foreach ($staging_file_content_array as $staging_file_content_line) {
		if (preg_match('/<DT><H3.+>(.+)<\/H3>/i', $staging_file_content_line, $category_match)) {
			if ($latest_addition === 1) {
				array_pop($categorys);
			}
			if (strlen($category_match[1]) <= 64) {
				array_push($categorys, [
					'title' => $category_match[1],
					'description' => ''
				]);
			} else {
				array_push($categorys, [
					'title' => mb_substr($category_match[1], 0, 16),
					'description' => $category_match[1]
				]);
			}
			$latest_addition = 1;
		} elseif (preg_match('/<DT><A HREF="(.+)" ADD_DATE.+>(.+)<\/A>/i', $staging_file_content_line, $link_match)) {
			if (strlen($link_match[2]) <= 64) {
				array_push($links, [
					'category' => count($categorys) - 1,
					'title' => $link_match[2],
					'url' => $link_match[1],
					'description' => ''
				]);
			} else {
				array_push($links, [
					'category' => count($categorys) - 1,
					'title' => mb_substr($link_match[2], 0, 16),
					'url' => $link_match[1],
					'description' => $link_match[2]
				]);
			}
			$latest_addition = 0;
		}
	}

	// 导入默认分类
	$category_data = [
		'fid' => 0,
		'weight' => 0,
		'title' => '导入的无分类链接',
		'font_icon' => 'fa-bookmark-o',
		'description' => '',
		'property' => $property
	];
	$state = $helper->addCategory_AuthRequired($category_data, true);
	if (is_int($state)) {
		$default_category_id = $state;
	} else {
		$helper->throwError(500, '导入失败，内部服务器错误！');
	}

	// 导入分类
	foreach ($categorys as $key => $category) {
		$category_data = [
			'fid' => 0,
			'weight' => 0,
			'title' => $category['title'],
			'font_icon' => 'fa-bookmark-o',
			'description' => $category['description'],
			'property' => $property
		];
		$state = $helper->addCategory_AuthRequired($category_data, true);
		if (is_int($state)) {
			$categorys[$key]['id'] = $state;
		} else {
			// 忽视错误并设置为默认分类
			$categorys[$key]['id'] = $default_category_id;
		}
	}

	// 导入链接
	foreach ($links as $link) {
		$category = $link['category'];
		$link_data = [
			'fid' => $categorys[$category]['id'],
			'weight' => 0,
			'title' => $link['title'],
			'url' => $link['url'],
			'url_standby' => '',
			'description' => $link['description'],
			'property' => $property
		];
		$state = $helper->addLink_AuthRequired($link_data);
	}

	$helper->returnSuccess();
}
