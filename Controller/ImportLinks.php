<?php
/**
 * 链接导入控制器
 * 进入 ImportLinks 流程需要 mbstring 支持
 *
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 *
 * @link		https://opennav.soraharu.com/
 */

require_once __DIR__ . '/../Public/index.php';

use Spatie\SimpleExcel\SimpleExcelReader;

// 获取分页参数
$page = empty($_GET['page']) ? '' : htmlspecialchars(trim($_GET['page']));

/**
 * 全局鉴权「Auth Safety」
 */
if (!$is_login) {
	$helper->throwError(403, '鉴权失败！');
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
		$staging_file_name = time() . '.links.' . $file_suffix;
		if (
			($file_suffix !== 'html' && $file_suffix !== 'xlsx' && $file_suffix !== 'csv') ||
			filesize($temp_file_directory) > 1024 * 1024 * 8
		) {
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
 * 导入链接文件
 */
if ($page === 'ImportLinks') {
	if (empty($_POST['property'])) {
		$_POST['property'] = 0;
	}
	if (empty($_POST['staging_file_name'])) {
		$helper->throwError(403, '参数不完整！');
	}
	// 获取文件名
	$staging_file_name = htmlspecialchars(trim($_POST['staging_file_name']));
	// 获取文件后缀
	$file_suffix = explode('.', $file_name);
	$file_suffix = strtolower(end($file_suffix));
	// 获取私有状态
	$property = intval($_POST['property']);
	// 判断文件是否存在
	if (!file_exists('../Cache/Upload/' . $staging_file_name)) {
		$helper->throwError(403, '文件不存在！');
	}
	// 设置导入任务运行时限
	ini_set('max_execution_time', 300); // 5 minutes

	if ($file_suffix === 'html' || $file_suffix === 'htm') {
		$helper->backupDatabase_AuthRequired()
			? $helper->emptyCategoriesTable_AuthRequired() &&
				$helper->emptyLinksTable_AuthRequired()
			: $helper->throwError(403, '数据库备份失败，导入被中止！');

		$links = []; // 链接组
		$categories = []; // 分类组
		$default_category_id = 0; // 默认分类 ID

		// 解析 HTML 数据
		$staging_file_content = file_get_contents('../Cache/Upload/' . $staging_file_name);
		$staging_file_content_array = explode("\n", $staging_file_content); // 分割文本
		$latest_addition = 0; // 0: link, 1: category
		foreach ($staging_file_content_array as $staging_file_content_line) {
			if (
				preg_match('/<DT><H3.+>(.+)<\/H3>/i', $staging_file_content_line, $category_match)
			) {
				if ($latest_addition === 1) {
					array_pop($categories);
				}
				if (strlen($category_match[1]) <= 64) {
					array_push($categories, [
						'title' => $category_match[1],
						'description' => ''
					]);
				} else {
					array_push($categories, [
						'title' => mb_substr($category_match[1], 0, 16),
						'description' => $category_match[1]
					]);
				}
				$latest_addition = 1;
			} elseif (
				preg_match(
					'/<DT><A HREF="(.+)" ADD_DATE.+>(.+)<\/A>/i',
					$staging_file_content_line,
					$link_match
				)
			) {
				if (strlen($link_match[2]) <= 64) {
					array_push($links, [
						'category' => count($categories) - 1,
						'title' => $link_match[2],
						'url' => $link_match[1],
						'description' => ''
					]);
				} else {
					array_push($links, [
						'category' => count($categories) - 1,
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
		foreach ($categories as $key => $category) {
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
				$categories[$key]['id'] = $state;
			} else {
				// 忽视错误并设置为默认分类
				$categories[$key]['id'] = $default_category_id;
			}
		}

		// 导入链接
		foreach ($links as $link) {
			$category = $link['category'];
			$link_data = [
				'fid' => $categories[$category]['id'],
				'weight' => 0,
				'title' => $link['title'],
				'url' => $link['url'],
				'url_standby' => '',
				'description' => $link['description'],
				'property' => $property
			];
			$state = $helper->addLink_AuthRequired($link_data);
		}

		// 检测默认分类是否存在链接
		if ($helper->countLinksByCategoryId_AuthRequired($default_category_id) === 0) {
			// 如不存在，则删除默认分类
			$helper->deleteCategory_AuthRequired($default_category_id);
		}

		$helper->returnSuccess();
	} elseif ($file_suffix === 'xlsx' || $file_suffix === 'csv') {
		$helper->backupDatabase_AuthRequired()
			? $helper->emptyCategoriesTable_AuthRequired() &&
				$helper->emptyLinksTable_AuthRequired()
			: $helper->throwError(403, '数据库备份失败，导入被中止！');

		$excel_reader = SimpleExcelReader::create('../Cache/Upload/' . $staging_file_name);
		$excel_reader
			->fromSheetName('categories')
			->getRows()
			->each(function (array $row_properties): void {
				if ($row_properties['fid'] === 0) {
					global $helper;
					$helper->addCategory_AuthRequired($row_properties);
				}
			});
		$excel_reader
			->fromSheetName('categories')
			->getRows()
			->each(function (array $row_properties): void {
				if ($row_properties['fid'] !== 0) {
					global $helper;
					$helper->addCategory_AuthRequired($row_properties);
				}
			});
		$excel_reader
			->fromSheetName('links')
			->getRows()
			->each(function (array $row_properties): void {
				global $helper;
				$helper->addLink_AuthRequired($row_properties);
			});

		$helper->returnSuccess();
	} else {
		$helper->throwError(403, '文件类型不正确！');
	}
}

exit();
