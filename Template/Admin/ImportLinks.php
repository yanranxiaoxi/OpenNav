<?php require_once __DIR__ . '/../../Public/index.php'; ?>
<?php require_once __DIR__ . '/../../Controller/Admin.php'; ?>
<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg"><!-- #TODO# -->
				支持导入现代浏览器导出的 <em>.html</em> 格式书签文件以及 <a href="#" target="_blank">预置格式</a> 的 <em>.xlsx</em> 文件，导入时会自动创建不存在的分类
			</div>
		</div>

		<!-- 上传 -->
		<div class="layui-form-item">
			<div class="layui-upload-drag" id="upload_html">
				<i class="layui-icon layui-icon-upload"></i>
				<p>点击上传，或将书签文件拖拽到此处</p>
			</div>
		</div>
		<!-- 上传 END -->

		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label">暂存文件名</label>
					<div class="layui-input-block">
						<input type="text" id="staging_file_name" name="staging_file_name" required lay-verify="required" readonly="readonly" placeholder="请上传书签文件" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">是否私有</label>
					<div class="layui-input-block">
						<input type="checkbox" name="property" value="1" lay-skin="switch" lay-text="是|否" />
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="import_links">开始导入</button>
				</div>

			</form>
		</div>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	layui.use(['form', 'upload'], function() {
		// 书签导入
		layui.form.on('submit(import_links)', function(data) {
			const loading_msg = layer.load(2, {time: 300 * 1000});
			layer.msg('正在导入书签，最多等待 300 秒 ...', {icon: 0});
			$.post('./index.php?c=ImportLinks&page=ImportLinks', data.field, function(data, status) {
				layer.close(loading_msg);
				// 如果导入成功
				if (data.code === 200) {
					$('#staging_file_name').val('');
					layer.msg('书签导入成功！', {icon: 1});
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});

		layui.upload.render({
			elem: '#upload_html',
			url: 'index.php?c=ImportLinks&page=UploadLinksFile',
			accept:'file',
			exts: 'html',
			auto: true,
			size: 8192,
			multiple: false,
			drag: true,
			done: function(data) {
				// 上传完毕回调
				if(data.code === 200) {
					$("#staging_file_name").val(data.data);
					layer.msg('文件上传成功！', {icon: 1});
				} else {
					layer.msg(data.message, {icon: 5});
					layer.close();
				}
			},
			error: function() {
				// 请求异常回调
			}
		});
	})
</script>

<?php exit(); ?>
