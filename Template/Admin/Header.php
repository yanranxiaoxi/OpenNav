<?php
if (!$is_login) {
	exit('鉴权失败！');
}
?>

<!DOCTYPE html>
<html lang="zh-cmn-Hans">
	<head>
		<title>OpenNav Portal</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="./node_modules/layui/dist/css/layui.css" />
		<link rel="stylesheet" href="./node_modules/font-awesome/css/font-awesome.min.css" />
		<link rel="stylesheet" href="./assets/admin/main.css?v=<?php echo VERSION; ?>" />
	</head>
	<body class="layui-layout-body">
		<div class="layui-layout layui-layout-admin">
			<div class="layui-header">
				<div class="layui-logo"><a href="./index.php?c=Admin" style="color: #009688;"><h2>OpenNav Portal</h2></a></div>
				<!-- 头部区域 -->
				<ul class="layui-nav layui-layout-left">
					<li class="layui-nav-item"><a href="./"><i class="layui-icon layui-icon-home"></i> 前台首页</a></li>
					<li class="layui-nav-item"><a href="./index.php?c=Admin&page=Categories"><i class="layui-icon layui-icon-list"></i> 分类列表</a></li>
					<li class="layui-nav-item"><a href="./index.php?c=Admin&page=Links"><i class="layui-icon layui-icon-link"></i> 链接列表</a></li>
					<li class="layui-nav-item"><a href="./index.php?c=Login&page=Logout"><i class="layui-icon layui-icon-logout"></i> 退出登录</a></li>
				</ul>
				<ul class="layui-nav layui-layout-right">
					<li class="layui-nav-item">
						<a href="javascript:;">
							<img src="<?php echo GRAVATAR_API_URL; ?>/avatar/<?php echo md5(EMAIL); ?>?s=80&d=wavatar&r=g" alt="头像" class="layui-nav-img" />
							<?php echo USERNAME; ?>
						</a>
						<dl class="layui-nav-child">
							<dd><a href="./index.php?c=User&page=Options">用户设置</a></dd>
							<dd><a href="./index.php?c=Login&page=Logout">退出登录</a></dd>
						</dl>
					</li>
				</ul>
			</div>
