<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder" style="padding-bottom: 3em;">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg"><!-- #TODO# -->
				时基验证（TOTP）配置方式，请参考：<a href="#" target="_blank" title="时基验证文档">时基验证 - OpenNav 文档</a>
			</div>
		</div>
		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<img src="<?php echo $totp_qrcode; ?>" alt="二维码" width="300" height="300" />
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">SecretKey</label>
					<div class="layui-input-block">
						<input type="text" name="totp_secret_key" readonly="readonly" value="<?php echo TOTP_SECRET_KEY; ?>" autocomplete="off" placeholder="TOTP SecretKey" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">TOTP Code</label>
					<div class="layui-input-block">
						<input type="number" name="totp_code" autocomplete="off" placeholder="请输入六位验证码" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="verify_code">验证配置</button>
					<button type="button" id="copy_totp_link" class="layui-btn layui-btn-primary" data-clipboard-text="<?php echo $totp_data; ?>">复制 TOTP 链接</button>
					<button class="layui-btn layui-btn-primary layui-border-red" lay-submit lay-filter="reset_totp">重置 TOTP SecretKey</button>
				</div>

			</form>
		</div>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	layui.use(['form'], function() {
		// 验证时基验证码配置
		layui.form.on('submit(verify_code)', function(data) {
			// 正则验证 TOTP Code
			const totp_code_regex = /^[0-9]{6}$/;
			if (!totp_code_regex.test(data.field.totp_code)) {
				layer.msg('TOTP Code 仅允许为 6 位数字！', {icon: 5});
				return false;
			}

			$.post('./index.php?c=TimeBaseValidator&page=VerifyCode', data.field, function(data, status) {
				// 如果设置成功
				if (data.code === 200) {
					layer.msg('配置正确！', {icon: 1});
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});

		layui.form.on('submit(reset_totp)', function(data) {
			$.post('./index.php?c=TimeBaseValidator&page=ResetSecretKey', data.field, function(data, status) {
				// 如果设置成功
				if (data.code === 200) {
					layer.msg('重置成功！请等待页面刷新 ...', {icon: 1});
					setTimeout(() => {
						window.location.reload();
					}, 500);
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});
	});

	const clipboard = new ClipboardJS('#copy_totp_link');
	clipboard.on('success', function(e) {
		layer.msg('复制成功！', {icon: 1});
	});
	clipboard.on('error', function(e) {
		layer.msg('复制失败！', {icon: 5});
	});
</script>
