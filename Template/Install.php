<?php require_once __DIR__ . '/../Public/index.php'; ?>
<?php require_once __DIR__ . '/../Controller/Install.php'; ?>

<!DOCTYPE html>
<html lang="zh-cmn-Hans">
	<head>
		<title>安装 OpenNav</title>
		<meta charset="utf-8" />
		<meta name="author" content="XiaoXi <admin@soraharu.com>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="./node_modules/layui/dist/css/layui.css" />
		<style>
			body {
				background-color: rgba(0, 0, 51, 0.8);
			}

			.login-logo {
				max-width: 400px;
				height: auto;
				margin-left: auto;
				margin-right: auto;
				margin-top: 5em;
			}

			.login-logo h1 {
				color: #FFFFFF;
				text-align: center;
			}
		</style>
	</head>
	<body>
		<div class="layui-container">
			<div class="layui-row">
				<div class="login-logo">
					<h1>安装 OpenNav</h1>
				</div>
				<div class="layui-col-md6 layui-col-md-offset3" style="margin-top: 4em;">
					<form class="layui-form layui-form-pane">

						<div class="layui-form-item">
							<label class="layui-form-label">用户名</label>
							<div class="layui-input-block">
								<input type="text" name="username" required lay-verify="required" placeholder="3-32 位的字母或数字" autocomplete="off" class="layui-input" />
							</div>
						</div>

						<div class="layui-form-item">
							<label class="layui-form-label">密码</label>
							<div class="layui-input-block">
								<input type="password" name="password" required lay-verify="required" placeholder="6-128 位字母、数字或特殊字符" autocomplete="off" class="layui-input" />
							</div>
						</div>

						<div class="layui-form-item">
							<label class="layui-form-label">确认密码</label>
							<div class="layui-input-block">
								<input type="password" name="password2" required lay-verify="required" placeholder="请再次输入密码" autocomplete="off" class="layui-input" />
							</div>
						</div>

						<div class="layui-form-item">
							<label class="layui-form-label">电子邮箱</label>
							<div class="layui-input-block">
								<input type="email" name="email" placeholder="可选，用于获取 Gravatar 头像" autocomplete="off" class="layui-input" />
							</div>
						</div>

						<div class="layui-form-item">
							<button class="layui-btn" lay-submit lay-filter="install_opennav" style="width: 100%;">开始安装</button>
						</div>

					</form>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="./node_modules/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="./node_modules/layui/dist/layui.js"></script>
		<script type="text/javascript">
			layui.use(['form'], function() {
				layui.form.on('submit(install_opennav)', function(data) {
					// 正则验证用户名
					const username_regex = /^[0-9a-zA-Z]{3,32}$/;
					if (!username_regex.test(data.field.username)) {
						layer.msg('用户名需要 3-32 位的字母或数字组合！', {icon: 5});
						return false;
					}

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

					// 正则验证电子邮箱
					const email_regex = /^[0-9a-zA-Z_-]+@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)+$/;
					if (data.field.email !== '' && !email_regex.test(data.field.email)) {
						layer.msg('请输入正确的电子邮箱！', {icon: 5});
						return false;
					}

					$.post('./index.php?c=Install&page=Install', data.field, function(data, status) {
						// 如果添加成功
						if (data.code === 200) {
							layer.msg(data.message, {icon: 1});
							setTimeout(() => {
								window.location.href = './index.php?c=Login';
							}, 2000);
						} else {
							layer.msg(data.message, {icon: 5});
						}
					});
					return false; // 阻止表单跳转
				});
			})
		</script>
	</body>
</html>

<? exit(); ?>
