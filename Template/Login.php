<?php require_once __DIR__ . '/../Public/index.php'; ?>
<?php require_once __DIR__ . '/../Controller/Login.php'; ?>

<!DOCTYPE html>
<html lang="zh-cmn-Hans">
	<head>
		<title>登录 OpenNav</title>
		<meta charset="utf-8" />
		<meta name="author" content="XiaoXi <admin@soraharu.com>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="./node_modules/layui/dist/css/layui.css" />
		<style>
			* {
				margin: 0;
				padding: 0;
			}

			.login {
				opacity: 0;
				width: 100vw;
				height: 100vh;
				background: url("./assets/images/login-background.svg") no-repeat center/cover;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			.login>.root {
				position: absolute;
				width: 70%;
				height: 600px;
				transition: all 0.3s;
				box-shadow: 0px 0px 10px rgba(250,250,250,0.227);
				border-radius: 15px;
				overflow: hidden;
				display: flex;
			}

			.login>.root .left {
				transition: all 0.3s;
				position: relative;
				width: 50%;
				background: #000;
			}

			.login>.root .left>.cover {
				position: absolute;
				display: block;
				width: 100%;
				height: 100%;
				object-fit: cover;
			}

			.login>.root .right {
				transition: all 0.5s;
				min-width: 550px;
				width: 50%;
				display: flex;
				flex-direction: column;
				background: #fbfbfb;
			}

			.login>.root .right>h2 {
				margin: 40px 20px 0px;
				text-align: center;
				font-size: 32px;
				font-family: "Source Han Sans CN-Bold", "Source Han Sans CN";
				font-weight: bold;
				color: #2a2a2a;
				line-height: 48px;
			}

			.login>.root .right>h2:hover {
				color: #0088ea;
			}

			.login>.root .right .login_frame {
				display: flex;
				justify-content: center;
			}

			.login>.root .right .login_box {
				margin-top: 50px;
				padding: 20px;
				width: 320px;
				height: 360px;
				background: #ffffff;
				box-shadow: 0px 3px 8px 1px rgba(0,0,0,0.16);
				border-radius: 16px 16px 16px 16px;
				opacity: 1;
			}

			.login>.root .right .login_box>h4 {
				width: 411px;
				height: 24px;
				font-size: 24px;
				font-family: "Source Han Sans CN-Regular", "Source Han Sans CN";
				font-weight: 400;
				color: #000000;
				line-height: 24px;
			}

			.login>.root .right .login_box>h6 {
				margin-top: 10px;
				width: 411px;
				height: 24px;
				font-size: 16px;
				font-family: "Helvetica Neue-常规体", "Helvetica Neue";
				font-weight: normal;
				color: #323232;
				line-height: 24px;
				margin-bottom: 30px;
			}

			.login>.root .right .login_box>form>.inp {
				margin-bottom: 15px;
				display: flex;
				flex-direction: column;
			}

			.login>.root .right .login_box>form>.inp>.label {
				height: 16px;
				font-size: 12px;
				font-family: "Source Han Sans CN-Bold", "Source Han Sans CN";
				font-weight: bold;
				color: #1a1a1a;
				line-height: 16px;
				margin-bottom: 5px;
			}

			.login>.root .right .login_box>form>.inp>input {
				width: calc(100% - 30px);
				height: 35px;
				border-radius: 8px 8px 8px 8px;
				opacity: 1;
				outline: none;
				border: 1px solid #bfbfbf;
				padding: 0px 15px;
			}

			.login>.root .right .login_box>form>.submit {
				margin-top: 25px;
			}

			.login>.root .right .login_box>form>.submit>input {
				width: 100%;
				height: 40px;
				background: #0088ea;
				outline: none;
				border: 1px solid #0088ea;
				border-radius: 8px;
				font-size: 16px;
				font-family: "Source Han Sans CN-Regular", "Source Han Sans CN";
				font-weight: 400;
				color: #ffffff;
			}

			.login .mobile {
				display: none;
			}

			@media screen and (max-width: 1278px) {
				.login .left {
					display: none;
				}

				.login .right {
					min-width: 100% !important;
					width: 100%;
				}
			}

			@media screen and (max-width: 658px) {
				.login .root {
					display: none !important;
				}

				.login .mobile {
					width: 100%;
					height: 100%;
					position: absolute;
					display: block;
				}

				.login .mobile>h1 {
					margin: 20px;
					color: #ffffff;
				}

				.login .mobile>form {
					position: relative;
					margin: 15px;
					padding: 15px;
					margin-top: 100px;
				}

				.login .mobile>form>.inp {
					margin-bottom: 15px;
					display: flex;
					flex-direction: column;
				}

				.login .mobile>form>.inp>.label {
					height: 16px;
					font-size: 13px;
					font-family: "Source Han Sans CN-Bold", "Source Han Sans CN";
					font-weight: bold;
					color: #1a1a1a;
					line-height: 16px;
					margin-bottom: 5px;
					color: #ffffff;
				}

				.login .mobile>form>.inp>input {
					width: calc(100% - 30px);
					height: 40px;
					border-radius: 8px 8px 8px 8px;
					opacity: 1;
					outline: none;
					border: 1px solid #bfbfbf;
					padding: 0px 15px;
				}

				.login .mobile>form>.submit {
					margin-top: 25px;
				}

				.login .mobile>form>.submit>input {
					width: 100%;
					height: 40px;
					background: #0088ea;
					outline: none;
					border: 1px solid #0088ea;
					border-radius: 8px;
					font-size: 16px;
					font-family: "Source Han Sans CN-Regular", "Source Han Sans CN";
					font-weight: 400;
					color: #ffffff;
				}
			}

			footer {
				width: 100%;
				position: absolute;
				z-index: 9;
				bottom: 10px;
				display: flex;
				font-size: 12px;
				align-items: center;
				justify-content: center;
				color: #9e9e9e;
			}

			footer>img {
				margin-right: 5px;
				width: 20px;
				height: 20px;
			}
		</style>
	</head>
	<body class="login">
		<div class="root">
			<section class="left">
				<img class="cover" src="./assets/images/login-image.png" />
			</section>
			<section class="right">
				<!-- PC 版的样式 -->
				<h2>OpenNav Portal</h2>
				<div class="login_frame">
					<div class="login_box">
						<h4>管理员登录</h4>
						<h6>欢迎回到 OpenNav 门户！</h6>
						<form method="post">
							<div class="inp">
								<span class="label">用户名</span>
								<input type="text" id="username" placeholder="请输入用户名" />
							</div>
							<div class="inp">
								<span class="label">密码</span>
								<input type="password" id="password" placeholder="请输入密码" />
							</div>
							<div class="inp">
								<span class="label">TOTP</span>
								<input type="number" id="totp_code" lay-verify="number" placeholder="不理解请留空" autocomplete="off" />
							</div>
							<div class="submit">
								<input type="submit" lay-submit lay-filter="login" class="submit" value="登录" />
							</div>
						</form>
					</div>
				</div>
			</section>
		</div>
		<div class="mobile">
			<!-- 手机版的样式 -->
			<h1>OpenNav Portal</h1>
			<form method="post">
				<div class="inp">
					<span class="label">用户名</span>
					<input type="text" id="mobile_username" placeholder="请输入用户名" />
				</div>
				<div class="inp">
					<span class="label">密码</span>
					<input type="password" id="mobile_password" placeholder="请输入密码" />
				</div>
				<div class="inp">
					<span class="label">TOTP</span>
					<input type="number" id="mobile_totp_code" lay-verify="number" placeholder="不理解请留空" autocomplete="off" />
				</div>
				<div class="submit">
					<input type="submit" lay-submit lay-filter="mobile_login" class="submit" value="登录" />
				</div>
			</form>
		</div>
		<footer>© <?php echo date("Y"); ?> Powered by <a style="color: #ffffff; padding-left: 6px;" href="https://github.com/yanranxiaoxi/OpenNav" target="_blank" title="OpenNav"> OpenNav</a></footer>

		<script type="text/javascript" src="./node_modules/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="./node_modules/layui/dist/layui.js"></script>
		<script type="text/javascript">
			// 加载完成后显示登录框
			window.onload = function() {
				document.querySelector('.login').style.opacity = 1;
			}
		</script>
		<script type="text/javascript">
			layui.use(['form'], function() {
				// 登录
				layui.form.on('submit(login)', function(data) {
					const username = $('#username').val();
					const password = $('#password').val();
					const totp_code = $('#totp_code').val();
					// 正则验证用户名
					const username_regex = /^[0-9a-zA-Z]{3,32}$/;
					if (username !== '' && !username_regex.test(username)) {
						layer.msg('用户名需要 3-32 位的字母或数字组合！', {icon: 5});
						return false;
					}

					// 正则验证密码
					const password_regex = /^[0-9a-zA-Z!@#$%^&*()-_\[\]\{\}<>~`\+=,.;:\/?|]{6,128}$/;
					if (password !== '' && !password_regex.test(password)) {
						layer.msg('密码需要 6-128 字母、数字或特殊字符！', {icon: 5});
						return false;
					}

					// 正则验证 TOTP Code
					const totp_code_regex = /^[0-9]{6}$/;
					if (totp_code !== '' && !totp_code_regex.test(totp_code)) {
						layer.msg('TOTP Code 为 6 位数字！', {icon: 5});
						return false;
					}

					if (username === '' && password === '' && totp_code === '') {
						layer.msg('请填写登录信息！', {icon: 5});
						return false;
					}

					$.post('./index.php?c=Login&page=Check', {username:username, password:password, totp_code:totp_code}, function(data, status) {
						// 如果验证成功
						if (data.code === 200) {
							window.location.href = './index.php?c=Admin';
						} else if (data.code === 201) {
							window.location.href = './';
						} else {
							layer.msg(data.message, {icon: 5});
						}
					});
					return false; // 阻止表单跳转
				});

				// 手机登录
				layui.form.on('submit(mobile_login)', function(data) {
					const username = $('#mobile_username').val();
					const password = $('#mobile_password').val();
					const totp_code = $('#mobile_totp_code').val();
					// 正则验证用户名
					const username_regex = /^[0-9a-zA-Z]{3,32}$/;
					if (username !== '' && !username_regex.test(username)) {
						layer.msg('用户名需要 3-32 位的字母或数字组合！', {icon: 5});
						return false;
					}

					// 正则验证密码
					const password_regex = /^[0-9a-zA-Z!@#$%^&*()-_\[\]\{\}<>~`\+=,.;:\/?|]{6,128}$/;
					if (password !== '' && !password_regex.test(password)) {
						layer.msg('密码需要 6-128 字母、数字或特殊字符！', {icon: 5});
						return false;
					}

					// 正则验证 TOTP Code
					const totp_code_regex = /^[0-9]{6}$/;
					if (totp_code !== '' && !totp_code_regex.test(totp_code)) {
						layer.msg('TOTP Code 为 6 位数字！', {icon: 5});
						return false;
					}

					if (username === '' && password === '' && totp_code === '') {
						layer.msg('请填写登录信息！', {icon: 5});
						return false;
					}

					$.post('./index.php?c=Login&page=Check', data.field, function(data, status) {
						// 如果验证成功
						if (data.code === 200) {
							window.location.href = './index.php?c=Admin';
						} else if (data.code === 201) {
							window.location.href = './';
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
