<?php
if (CONFIG_VERSION !== VERSION) {
	$current_version = CONFIG_VERSION;
	while ($current_version !== VERSION) {
		require_once('../Data/Upgrade/' . $current_version . '.php');
	}
	exit('更新已完成，请刷新页面！');
}
