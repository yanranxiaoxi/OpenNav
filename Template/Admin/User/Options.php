<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder" style="padding-bottom: 3em;">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg"><!-- #TODO# -->
				<p>用户设置配置方式，请参考：<a href="#" target="_blank" title="用户设置文档">用户设置 - OpenNav 文档</a></p>
				<p>验证方式简要说明：</p>
				<p>验证方式主要指的是登录时将要填写于登录验证表单的信息，选择的信息越复杂，安全性越高。允许以下四种方式：</p>
				<p>1. 用户名 + 密码（默认）</p>
				<p>2. 用户名 + 密码 + TOTP Code（安全性最高，需要 <a href="./index.php?c=TimeBaseValidator&page=Setup" target="_self" title="用户设置文档">配置时基验证设备</a> ）</p>
				<p>3. 用户名 + TOTP Code（安全性较低，选用此方式仍然允许使用 用户名 + 密码 方式登录）</p>
				<p>4. TOTP Code（安全性极低，选用此方式仍然允许使用 用户名 + 密码 方式登录）</p>
			</div>
		</div>
		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label">用户名</label>
					<div class="layui-input-block">
						<input type="text" name="username" value="<?php echo USERNAME; ?>" required lay-verify="required" autocomplete="off" placeholder="请输入用户名" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">电子邮箱</label>
					<div class="layui-input-block">
						<input type="email" name="email" value="<?php echo EMAIL; ?>" autocomplete="off" placeholder="请输入电子邮箱" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">验证方式</label>
					<div class="layui-input-block">
						<input type="checkbox" name="login_authentication_mode_username" value="1" title="用户名" <?php echo (LOGIN_AUTHENTICATION_MODE === 3 || LOGIN_AUTHENTICATION_MODE === 5 || LOGIN_AUTHENTICATION_MODE === 7) ? 'checked' : ''; ?> />
						<input type="checkbox" name="login_authentication_mode_password" value="2" title="密码" <?php echo (LOGIN_AUTHENTICATION_MODE === 3 || LOGIN_AUTHENTICATION_MODE === 7) ? 'checked' : ''; ?> />
						<input type="checkbox" name="login_authentication_mode_totp_code" value="4" title="TOTP Code" <?php echo (LOGIN_AUTHENTICATION_MODE === 4 || LOGIN_AUTHENTICATION_MODE === 5 || LOGIN_AUTHENTICATION_MODE === 7) ? 'checked' : ''; ?> />
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="set_options">保存设置</button>
					<a class="layui-btn layui-btn-primary layui-border-green" target="_self" title="修改密码" href="#"><i class="fa fa-key"></i> 修改密码</a>
					<a class="layui-btn layui-btn-primary layui-border-green" target="_self" title="配置时基验证" href="./index.php?c=TimeBaseValidator&page=Setup"><i class="fa fa-clock-o"></i> 配置时基验证（TOTP）</a>
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
		layui.form.on('submit(set_options)', function(data) {
			data.field.login_authentication_mode = 0;
			if (data.field.login_authentication_mode_username === '1') {
				data.field.login_authentication_mode = data.field.login_authentication_mode + 1;
			}
			if (data.field.login_authentication_mode_password === '2') {
				data.field.login_authentication_mode = data.field.login_authentication_mode + 2;
			}
			if (data.field.login_authentication_mode_totp_code === '4') {
				data.field.login_authentication_mode = data.field.login_authentication_mode + 4;
			}
			if (data.field.login_authentication_mode !== 3 && data.field.login_authentication_mode !== 4 && data.field.login_authentication_mode !== 5 && data.field.login_authentication_mode !== 7) {
				layer.msg('验证方式的组合不合法！', {icon: 5});
				return false;
			}
			$.post('./index.php?c=User&page=SetOptions', data.field, function(data, status) {
				// 如果设置成功
				if (data.code === 200) {
					layer.msg('设置成功！', {icon: 1});
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});
	})
</script>
