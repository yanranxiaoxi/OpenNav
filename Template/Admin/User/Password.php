<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder" style="padding-bottom: 3em;">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg">
				<p>密码要求为 6-128 位字母、数字或特殊字符</p>
			</div>
		</div>
		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label">新密码</label>
					<div class="layui-input-block">
						<input type="password" name="password" required lay-verify="required" autocomplete="off" placeholder="请输入新密码" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">确认新密码</label>
					<div class="layui-input-block">
						<input type="password" name="password2" required lay-verify="required" autocomplete="off" placeholder="请再次输入新密码" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="set_password">设置密码</button>
				</div>

			</form>
		</div>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	layui.use(['form'], function() {
		// 设置用户设置选项
		layui.form.on('submit(set_password)', function(data) {
			// 正则验证密码
			const password_regex = /^[0-9a-zA-Z!@#$%^&*()-_\[\]\{\}<>~`\+=,.;:\/?|]{6,128}$/;
			if (!password_regex.test(data.field.password)) {
				layer.msg('密码需要 6-128 字母、数字或特殊字符！', {icon: 5});
				return false;
			}
			if (data.field.password !== data.field.password2) {
				layer.msg('两次输入的密码不一致！', {icon: 5});
				return false;
			}

			$.post('./index.php?c=User&page=SetPassword', data.field, function(data, status) {
				// 如果设置成功
				if (data.code === 200) {
					layer.msg('设置成功！请等待页面跳转 ...', {icon: 1});
					setTimeout(() => {
						window.location.href = './index.php?c=Login';
					}, 500);
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});
	})
</script>
