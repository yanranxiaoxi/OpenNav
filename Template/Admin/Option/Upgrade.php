<?php require_once __DIR__ . '/../../../Public/index.php'; ?>
<?php require_once __DIR__ . '/../../../Controller/Admin.php'; ?>
<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder" style="padding-bottom: 3em;">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg">
				您可以 <a href="./index.php?c=Admin&page=Subscribe" target="_self" title="订阅授权">订阅授权</a> 服务，订阅后支持一键更新。
			</div>
		</div>
		<!-- 说明提示框 END -->
		<!-- 更新表格 -->
		<div class="layui-col-lg6">
			<h2 style = "margin-bottom: 1em;">更新 OpenNav</h2>
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label">当前版本</label>
					<div class="layui-input-block">
					<input type="text" name="current_version" id="current_version" readonly="readonly" value="<?php echo VERSION; ?>" autocomplete="off" placeholder="获取中 ..." class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">最新版本</label>
					<div class="layui-input-block">
					<input type="text" name="latest_version" id="latest_version" readonly="readonly" value="" autocomplete="off" placeholder="获取中 ..." class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">更新日期</label>
					<div class="layui-input-block">
					<input type="text" name="latest_version_date" id="latest_version_date" readonly="readonly" value="" autocomplete="off" placeholder="获取中 ..." class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">下载地址</label>
					<div class="layui-input-block">
					<input type="text" name="download_link" id="download_link" readonly="readonly" value="" autocomplete="off" placeholder="获取中 ..." class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">安装缓存</label>
					<div class="layui-input-block">
					<input type="text" name="download_cache" id="download_cache" readonly="readonly" value="" autocomplete="off" placeholder="获取中 ..." class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="do_upgrade">一键更新</button>
					<button class="layui-btn layui-btn-primary layui-border-red" lay-submit lay-filter="do_reinstall">修复程序（重新安装）</button>
					<a class="layui-btn layui-btn-primary" rel="nofollow" target="_blank" title="下载软件包" href="#"><i class="fa fa-cloud-download"></i> 下载软件包</a>
				</div>

			</form>
		</div>
		<!-- 更新表格 END -->
	</div>
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	// 获取最新版本
	$.post('./index.php?c=Upgrade&page=CheckLatestVersion', function(data, status) {
		if (data.code === 200) {
			const releasedDate = new Date(data.data.released_at);
			$("#latest_version").val(data.data.version);
			$("#latest_version_date").val([
				releasedDate.getFullYear().toString(),
				(releasedDate.getMonth() + 1).toString(),
				releasedDate.getDate().toString(),
			].join('-'));
			$("#download_link").val(data.data.download_link);
			if (data.data.download_cache) {
				$("#download_cache").val('true (v' + data.data.version + ')');
			} else {
				$("#download_cache").val('false');
			}
		} else {
			$("#latest_version").val(data.message);
			$("#download_link").val(data.message);
		}
	});

	layui.use(['form'], function() {
		// 一键更新选项
		layui.form.on('submit(do_upgrade)', function(data) {
			const loading_msg = layer.load(2, {time: 3 * 60 * 1000});
			layer.msg('正在下载软件包，最多等待 3 分钟 ...', {icon: 4});
			$.post('./index.php?c=Upgrade&page=GetPackage', data.field, function(data, status) {
				layer.close(loading_msg);
				// 如果下载完成
				if (data.code === 200) {
					$('#download_cache').val('true (v' + data.data.version + ')');
					layer.msg('软件包下载完成！', {icon: 1});
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});
	})
</script>

<?php exit(); ?>
