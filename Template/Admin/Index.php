<?php require_once __DIR__ . '/../../Public/index.php'; ?>
<?php require_once __DIR__ . '/../../Controller/Admin.php'; ?>
<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body place-holder">
	<!-- 内容主体区域 -->
	<div style="padding: 15px;">
		<div class="layui-container" style="margin-top: 2em;">

			<div class="layui-row layui-col-space18">
				<div class="layui-col-lg4">
					<div class="admin-msg">当前版本：<span id="current_version"><?php echo VERSION; ?></span>
					<span id="update_message" style="display: none;">（<a style="color: #ff5722;" href="https://github.com/yanranxiaoxi/OpenNav/releases" title="下载最新版 OpenNav" rel="nofollow" target="_blank" id="current_version">有可用更新</a>）</span>
				</div>
			</div>
			<div class="layui-col-lg4">
				<div class="admin-msg">
					最新版本：<span><span id="getting_version">获取中 ...</span><a href="https://github.com/yanranxiaoxi/OpenNav/releases" title="下载最新版 OpenNav" target="_blank" id="latest_version"></a></span>
					<!-- #TODO#（<a href="./index.php?c=Admin&page=Subscribe" title="订阅后可一键更新" target="_self">一键更新</a>） -->
				</div>
			</div>
			<div class="layui-col-lg4">
				<div class="admin-msg">项目仓库：<a href="https://github.com/yanranxiaoxi/OpenNav" title="前往 OpenNav 项目 GitHub 仓库" rel="nofollow" target="_blank">yanranxiaoxi/OpenNav</a></div>
			</div>
			<div class="layui-col-lg4">
				<div class="admin-msg">作者博客：<a href="https://tech.soraharu.com/" title="前往 OpenNav 作者技术博客" rel="nofollow" target="_blank">https://tech.soraharu.com/</a></div>
			</div>
			<div class="layui-col-lg4">
				<div class="admin-msg">打赏咖啡：<a href="https://www.buymeacoffee.com/yanranxiaoxi" title="前往打赏页" rel="nofollow" target="_blank">By Me A Coffee</a></div>
			</div>

			</div>
		</div>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	// 获取最新版本
	$.post('./index.php?c=Upgrade&page=CheckLatestVersion', function(data, status) {
		$("#getting_version").hide();
		if (data.code === 200) {
			$("#latest_version").text(data.version);
			let latest_version = data.version.split('.');
			let current_version = $("#current_version").text().split('.');
			latest_version[0] = parseInt(latest_version[0]);
			latest_version[1] = parseInt(latest_version[1]);
			latest_version[2] = parseInt(latest_version[2]);
			current_version[0] = parseInt(current_version[0]);
			current_version[1] = parseInt(current_version[1]);
			current_version[2] = parseInt(current_version[2]);
			if (latest_version[0] > current_version[0]) {
				$("#update_message").show();
			} else if (latest_version[1] > current_version[1]) {
				$("#update_message").show();
			} else if (latest_version[2] > current_version[2]) {
				$("#update_message").show();
			}
		} else {
			$("#latest_version").text(data.message);
		}
	});
</script>

<?php exit(); ?>
