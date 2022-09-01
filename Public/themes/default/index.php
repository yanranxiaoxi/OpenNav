<!DOCTYPE html>
<html lang="zh-cmn-Hans">
	<head>
		<title><?php echo $options_settings_site['title']; ?> - <?php echo $options_settings_site['subtitle']; ?></title>
		<meta charset="utf-8" />
		<meta name="author" content="XiaoXi <admin@soraharu.com>" />
		<meta name="keywords" content="<?php echo $options_settings_site['keywords']; ?>" />
		<meta name="description" content="<?php echo $options_settings_site['description']; ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="./node_modules/mdui/dist/css/mdui.min.css" />
		<link rel="stylesheet" href="./node_modules/jquery-contextmenu/dist/jquery.contextMenu.min.css" />
		<link rel="stylesheet" href="./node_modules/font-awesome/css/font-awesome.min.css" />
		<link rel="stylesheet" href="./themes/<?php echo $options_theme; ?>/assets/main.css?v=<?php echo $theme_info['version']; ?>" />
		<?php echo $options_settings_site['custom_header']; ?>
		<style>
			<?php if ($theme_config['show_link_description'] === false) { ?>
			.link-content {
				display: none;
			}

			.link-line {
				height: 56px;
			}
			<?php } ?>
		</style>
	</head>
	<body onload="focusSearchBar();" class="mdui-drawer-body-left mdui-appbar-with-toolbar <?php echo $theme_layout; ?> mdui-theme-primary-indigo mdui-theme-accent-pink mdui-loaded">
		<!-- 导航工具 -->
		<header class="mdui-appbar mdui-appbar-fixed">
			<div class="mdui-toolbar mdui-color-theme">
				<span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#drawer', swipe: true}"><i class="mdui-icon material-icons">menu</i></span>
				<a href="./" class="mdui-typo-headline" title="<?php echo $options_settings_site['title']; ?>"><span class="mdui-typo-title default-title"><h1><?php echo $options_settings_site['title']; ?></h1></span></a>
				<div class="mdui-toolbar-spacer"></div>
				<!-- 搜索框 -->
				<div class="mdui-col-md-3 mdui-col-xs-6">
					<div class="mdui-textfield mdui-textfield-floating-label">
						<input id="top_search_bar" onkeydown="topSearchBarSearch();" class="mdui-textfield-input mdui-text-color-white" placeholder="输入关键词进行搜索" type="text" />
						<i class="mdui-icon material-icons" style="position: absolute; right: 2px;">search</i>
					</div>
				</div>
				<!-- 搜索框 END -->
				<a class="mdui-hidden-xs" href="https://github.com/yanranxiaoxi/OpenNav" rel="nofollow" target="_blank" class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-tooltip="{content: '查看 GitHub'}">
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 36 36" enable-background="new 0 0 36 36" xml:space="preserve" class="mdui-icon" style="width: 24px; height: 24px;">
						<path fill-rule="evenodd" clip-rule="evenodd" fill="#ffffff" d="M18,1.4C9,1.4,1.7,8.7,1.7,17.7c0,7.2,4.7,13.3,11.1,15.5
							c0.8,0.1,1.1-0.4,1.1-0.8c0-0.4,0-1.4,0-2.8c-4.5,1-5.5-2.2-5.5-2.2c-0.7-1.9-1.8-2.4-1.8-2.4c-1.5-1,0.1-1,0.1-1
							c1.6,0.1,2.5,1.7,2.5,1.7c1.5,2.5,3.8,1.8,4.7,1.4c0.1-1.1,0.6-1.8,1-2.2c-3.6-0.4-7.4-1.8-7.4-8.1c0-1.8,0.6-3.2,1.7-4.4
							c-0.2-0.4-0.7-2.1,0.2-4.3c0,0,1.4-0.4,4.5,1.7c1.3-0.4,2.7-0.5,4.1-0.5c1.4,0,2.8,0.2,4.1,0.5c3.1-2.1,4.5-1.7,4.5-1.7
							c0.9,2.2,0.3,3.9,0.2,4.3c1,1.1,1.7,2.6,1.7,4.4c0,6.3-3.8,7.6-7.4,8c0.6,0.5,1.1,1.5,1.1,3c0,2.2,0,3.9,0,4.5
							c0,0.4,0.3,0.9,1.1,0.8c6.5-2.2,11.1-8.3,11.1-15.5C34.3,8.7,27,1.4,18,1.4z">
						</path>
					</svg>
				</a>
				<?php if ($is_login) { ?>
				<a class="mdui-hidden-xs" href="./index.php?c=Admin" title="后台管理" target="_blank" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">account_circle</i></a>
				<?php } else { ?>
				<a class="mdui-hidden-xs" href="./index.php?c=Login" title="登录 OpenNav" target="_blank" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">account_circle</i></a>
				<?php } ?>
			</div>
		</header>
		<!-- 导航工具 END -->

		<!-- 添加链接按钮 -->
		<!-- #TODO#
		<?php if ($is_login) { ?>
		<div class="mdui-hidden-xs" style="position: fixed; right: 20px; bottom: 70px; z-index: 1000;">
			<button title="添加链接" id="add" class="mdui-fab mdui-color-theme-accent mdui-ripple mdui-fab-mini"><i class="mdui-icon material-icons">add</i></button>
		</div>
		<?php } ?>
		-->
		<!-- 添加链接按钮 END -->

		<!-- 返回顶部按钮 -->
		<div style="position: fixed; right: 20px; bottom: 20px; z-index: 1000;">
			<button title="返回顶部" onclick="goTop();" class="mdui-fab mdui-ripple mdui-fab-mini"><i class="mdui-icon material-icons go-top-botton">arrow_drop_up</i></button>
		</div>
		<!-- 返回顶部按钮 END -->

		<!-- 左侧抽屉导航 -->
		<div class="mdui-drawer" id="drawer">
			<ul class="mdui-list">
				
				<!-- 遍历一级分类 -->
				<?php foreach ($parent_categorys as $parent_category_value) { ?>
				<div class="mdui-collapse" mdui-collapse>
					<div class="mdui-collapse-item">
						<div class="mdui-collapse-item-header">
							<a href="#category-<?php echo $parent_category_value['id']; ?>">
								<li class="mdui-list-item mdui-ripple">
									<div class="mdui-list-item-content category-title"><i class="fa <?php echo $parent_category_value['font_icon']; ?>"></i> <?php echo htmlspecialchars_decode($parent_category_value['title']); ?></div>
									<i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
								</li>
							</a>
						</div>
						<div class="mdui-collapse-item-body">
							<ul>
								<!-- 遍历二级分类 -->
								<?php foreach ($child_categorys[$parent_category_value['id']] as $child_category_value) { ?>
								<a href="#category-<?php echo $child_category_value['id']; ?>">
									<li class="mdui-list-item mdui-ripple" style="margin-left: -4.3em;">
										<div class="mdui-list-item-content child-category">
											<i class="fa <?php echo $child_category_value['font_icon']; ?>"></i><?php echo htmlspecialchars_decode($child_category_value['title']); ?>
										</div>
									</li>
								</a>
								<?php } ?>
							</ul>
						</div>
						<!-- 遍历二级分类 END -->
					</div>
				</div>
				<?php } ?>

				<!-- 华丽的分割线 -->
				<div class="mdui-divider"></div>
				<!-- 华丽的分割线 END -->

				<?php if ($is_login) { ?>
				<a href="./index.php?c=Login" title="登录" class="mdui-hidden-sm-up">
					<li class="mdui-list-item mdui-ripple">
						<div class="mdui-list-item-content category-title"><i class="fa fa-dashboard"></i> 登录</div>
					</li>
				</a>
				<?php } else { ?>
				<a href="./index.php?c=Login&page=Logout" title="退出" class="mdui-hidden-sm-up">
					<li class="mdui-list-item mdui-ripple">
						<div class="mdui-list-item-content category-title"><i class="fa fa-dashboard"></i> 退出</div>
					</li>
				</a>
				<?php } ?>

				<!-- 切换主题 -->
				<a href="javascript:;" onclick="changeTheme();" title="点击切换主题风格">
					<li class="mdui-list-item mdui-ripple">
						<div class="mdui-list-item-content category-title"><i class="fa fa-adjust"></i> 切换主题</div>
					</li>
				</a>
				<!-- 切换主题 END -->
			</ul>
		</div>
		<!-- 左侧抽屉导航 END -->

		<!-- 正文内容部分 -->
		<div class="<?php echo ($theme_config['full_width_mode'] === true) ? "mdui-container" : "mdui-container-fluid"; ?>">
			<div class="mdui-row">
				<!-- 遍历分类目录 -->
				<?php
				foreach ($categorys as $category_value) {
					// 如果分类是私有的
					if ($category_value['property'] === 1) {
						$property_private_favicon = ' <i class="fa fa-expeditedssl" style="color: #5fb878;"></i>';
					} else {
						$property_private_favicon = '';
					}
				?>
				<div id="category-<?php echo $category_value['id']; ?>" class="mdui-col-xs-12 mdui-typo-title cat-title">
					<i class="fa <?php echo $category_value['font_icon']; ?>"></i>
					<?php echo htmlspecialchars_decode($category_value['title']); ?><?php echo $property_private_favicon; ?>
					<span class="mdui-typo-caption"><?php echo htmlspecialchars_decode($category_value['description']); ?></span>
				</div>
				<!-- 遍历链接 -->
				<?php
				foreach ($links[$category_value['id']] as $link_value) {
					// 默认描述
					$link_value['description'] = empty($link_value['description']) ? '作者没有填写描述' : htmlspecialchars_decode($link_value['description']);
				?>
				<a href="./index.php?c=Click&id=<?php echo $link_value['id']; ?>" target="_blank" title="<?php echo $link_value['description']; ?>">
					<div class="mdui-col-lg-2 mdui-col-md-3 mdui-col-sm-4 mdui-col-xs-6 link-space" id="id_<?php echo $link_value['id']; ?>" link-title="<?php echo htmlspecialchars_decode($link_value['title']); ?>" link-url="<?php echo $link_value['url']; ?>">
						<!-- 用来搜索匹配使用 -->
						<span style="display: none;"><?php echo $link_value['url']; ?></span>
						<!-- 用来搜索匹配使用 END -->
						<!-- 定义一个卡片 -->
						<div class="mdui-card link-line mdui-hoverable">
							<!-- 如果是私有链接，则显示角标 -->
							<?php if ($link_value['property'] === 1) { ?>
							<div class="angle">
								<span> </span>
							</div>
							<?php } ?>
							<!-- 角标 END -->
							<div class="mdui-card-primary" style="padding-top: 16px;">
								<div class="mdui-card-primary-title link-title">
									<!-- 网站图标显示方式 -->
									<?php if ($theme_config['online_favicon'] === true) { ?>
									<img src="./index.php?c=Favicon&page=Online&url=<?php echo $link_value['url']; ?>" width="16" height="16" />
									<?php } else { ?>
									<img src="./index.php?c=Favicon&page=Offline&title=<?php echo $link_value['title']; ?>" width="16" height="16" />
									<?php } ?>
									<span class="link_title"><?php echo htmlspecialchars_decode($link_value['title']); ?></span>
								</div>
							</div>
							<!-- 卡片内容 END -->
							<div class="mdui-card-content" style="padding-top: 0px;"><span class="link-content"><?php echo htmlspecialchars_decode($link_value['description']); ?></span></div>
						</div>
						<!-- 卡片 END -->
					</div>
				</a>
				<?php } ?>
				<!-- 遍历链接 END -->
				<?php } ?>
			</div>

		</div>
		<div class="mdui-divider" style="margin-top: 2em;"></div>
		<!-- 正文内容部分 END -->
		<!-- Footer 部分 -->
		<footer>
			<?php if (empty($options_settings_site['custom_footer'])) { ?>
			© <?php echo date("Y"); ?> Powered by <a target="_blank" href="https://github.com/yanranxiaoxi/OpenNav" title="OpenNav" rel="nofollow">OpenNav</a>. The author is <a href="https://soraharu.com/" target="_blank">XiaoXi</a>.
			<?php } else {
				echo htmlspecialchars_decode($options_settings_site['custom_footer']);
			} ?>
		</footer>
		<!-- Footer 部分 END -->
	</body>
	<script type="text/javascript" src="./node_modules/mdui/dist/js/mdui.min.js"></script>
	<script type="text/javascript" src="./node_modules/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="./node_modules/layui-layer-src/dist/layer.js"></script>
	<script type="text/javascript" src="./node_modules/jquery-contextmenu/dist/jquery.contextMenu.min.js"></script>
	<script type="text/javascript" src="./node_modules/holmes.js/js/holmes.js"></script>
	<script type="text/javascript" src="./themes/<?php echo $options_theme; ?>/assets/main.js?v=<?php echo $theme_info['version']; ?>"></script>
</html>
