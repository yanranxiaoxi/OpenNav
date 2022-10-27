<?php

require_once __DIR__ . '/../../Public/index.php';

if (CONFIG_VERSION !== VERSION) {
	$current_version = CONFIG_VERSION;
	$need_reflush = false;
	while ($current_version !== VERSION && $need_reflush !== true) {
		require_once '../Binary/Upgrade/Version/' . $current_version . '.php';
	}
	exit('更新已完成，请刷新页面！');
}
