<!DOCTYPE html>
<html lang="zh-cmn-Hans">
	<head>
		<title><?php echo $link['title']; ?> - <?php echo $options_settings_site['title']; ?></title>
		<meta charset="utf-8" />
		<meta name="keywords" content="<?php echo $link['title']; ?>" />
		<meta name="description" content="<?php echo $link['description']; ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css" />
		
		<style>
			.prevent-overflow {
				width: 260px;
				overflow: hidden; /* 超出部分隐藏 */
				white-space: nowrap; /* 不换行 */
				text-overflow: ellipsis; /* 超出部分文字以...显示 */
			}

			.ad-banner img {
				max-width: 100%;
				padding-top: 1em;
				padding-bottom: 1em;
			}

			#menu {
				width: 100%;
				background-color: #343a40!important;
			}
		</style>
		<?php echo $options_settings_site['custom_header']; ?>
		<?php
		if ($is_login) {
			header("Refresh:" . $options_settings_transition_page['admin_stay_time'] . "; url=" . $link['url']);
		} else {
			header("Refresh:" . $options_settings_transition_page['visitor_stay_time'] . "; url=" . $link['url']);
		}
		?>
	</head>
	<body>
		<div id="menu">
			<div class="container">
				<div class="row">
					<div class="col-sm-8 offset-sm-2">
						<!-- 顶部导航菜单 -->
						<nav class="navbar navbar-expand-md bg-dark navbar-dark">
							<a class="navbar-brand" href="./"><?php echo $options_settings_site['title']; ?></a>
							<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
								<span class="navbar-toggler-icon"></span>
							</button>
							<div class="collapse navbar-collapse" id="collapsibleNavbar">
								<ul class="navbar-nav">
									<!-- 输出自定义菜单 -->
									<?php echo htmlspecialchars_decode($options_settings_transition_page['menu']); ?>
									<!-- 输出自定义菜单 END -->
								</ul>
							</div>
						</nav>
						<!-- 顶部导航菜单 END -->
					</div>
				</div>
			</div>
		</div>
		<div class="container" style="margin-top: 2em;">
			<!-- 顶部广告 -->
			<div class="row">
				<div class="col-sm-8 offset-sm-2 ad-banner">
					<?php echo htmlspecialchars_decode($options_settings_transition_page['ad_top']); ?>
				</div>
			</div>
			<!-- 顶部广告 END -->
			<div class="row">
				<div class="col-sm-8 offset-sm-2">
					<!-- 表格 -->
					<h2>链接信息：</h2>
					<table class="table">
						<tbody>

							<tr class="table-info">
								<td width="170">标题</td>
								<td><?php echo $link['title']; ?></td>
							</tr>

							<tr class="table-info">
								<td>描述</td>
								<td><?php echo $link['description']; ?></td>
							</tr>

							<tr class="table-info">
								<td>链接</td>
								<td>
									<div class="prevent-overflow">
										<a href="<?php echo $link['url']; ?>" rel="nofollow" title="<?php echo $link['title']; ?>"><?php echo $link['url']; ?></a>
									</div>
								</td>
							</tr>

						</tbody>
					</table>
					<!-- 表格 END -->

				</div>
			</div>
			<!-- 底部广告 -->
			<div class="row">
				<div class="col-sm-8 offset-sm-2 ad-banner">
				<?php echo htmlspecialchars_decode($options_settings_transition_page['ad_bottom']); ?>
				</div>
			</div>
			<!-- 底部广告 END -->
			<!-- 底部 Footer -->
			<div class="row">
				<div class="col-sm-8 offset-sm-2">
					<hr />
					<div class="xcdn-footer">
						<?php if (empty($options_settings_site['custom_footer'])) { ?>
							© <?php echo date("Y"); ?> Powered by <a target="_blank" href="https://github.com/yanranxiaoxi/OpenNav" title="OpenNav" rel="nofollow">OpenNav</a>. The author is <a href="https://soraharu.com/" target="_blank">XiaoXi</a>.
						<?php } else {
							echo htmlspecialchars_decode($options_settings_site['custom_footer']);
						} ?>
					</div>
				</div>
			</div>
			<!-- 底部 Footer END -->
		</div>
		<script type="text/javascript" src="./node_modules/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
	</body>
</html>
