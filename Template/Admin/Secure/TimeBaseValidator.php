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
					<label class="layui-form-label">TOTP Code</label>
					<div class="layui-input-block">
						<input type="number" name="totp_code" required lay-verify="required|number" autocomplete="off" placeholder="请输入六位验证码" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="verify_code">验证配置</button>
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
	})
</script>
