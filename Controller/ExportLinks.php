<?php
/**
 * 链接导出控制器
 *
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 *
 * @link		https://opennav.soraharu.com/
 */

require_once __DIR__ . '/../Public/index.php';

use Spatie\SimpleExcel\SimpleExcelWriter;

// 获取分页参数
$page = empty($_GET['page']) ? '' : htmlspecialchars(trim($_GET['page']));

/**
 * 全局鉴权「Auth Safety」
 */
if (!$is_login) {
	$helper->throwError(403, '鉴权失败！');
}

/**
 * 导出链接文件
 */
if ($page === 'ExportLinks') {
	$excel_writer_categories = SimpleExcelWriter::create(
		'../Cache/Export/OpenNavExport.Categories.xlsx'
	);
	$categories = $helper->getCategoriesIdFidWeightTitleFonticonDescriptionProperty_AuthRequired();
	$excel_writer_categories->addRows($categories);

	$excel_writer_links = SimpleExcelWriter::create('../Cache/Export/OpenNavExport.Links.xlsx');
	$links = $helper->getLinksIdFidWeightTitleUrlUrlstandbyDescriptionProperty_AuthRequired();
	$excel_writer_links->addRows($links);

	copy(
		'../Binary/Export/OpenNavExport.zh-cmn-Hans.此文件用于编辑与上传.xlsx',
		'../Cache/Export/OpenNavExport.zh-cmn-Hans.此文件用于编辑与上传.xlsx'
	);
}

exit();
