<?php require_once __DIR__ . '/../../../Public/index.php'; ?>
<?php require_once __DIR__ . '/../../../Controller/Admin.php'; ?>
<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder" style="padding-bottom: 3em;">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg"><!-- #TODO# -->
				站点设置配置方式，请参考：<a href="#" target="_blank" title="站点设置文档">站点设置 - OpenNav 文档</a>
			</div>
		</div>
		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label">网站标题</label>
					<div class="layui-input-block">
						<input type="text" name="title" value="<?php echo $options_settings_site['title']; ?>" required lay-verify="required" autocomplete="off" placeholder="请输入网站标题" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">网站 LOGO</label>
					<div class="layui-input-block">
						<input type="text" name="logo" value="<?php echo $options_settings_site['logo']; ?>" autocomplete="off" placeholder="网站 LOGO 地址，部分主题可能不支持" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">副标题</label>
					<div class="layui-input-block">
						<input type="text" name="subtitle" value="<?php echo $options_settings_site['subtitle']; ?>" required lay-verify="required" autocomplete="off" placeholder="请输入副标题" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">关键词</label>
					<div class="layui-input-block">
						<input type="text" name="keywords" value="<?php echo $options_settings_site['keywords']; ?>" autocomplete="off" placeholder="请输入网站关键词，用英文半角逗号分隔" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item layui-form-text">
					<label class="layui-form-label">网站描述</label>
					<div class="layui-input-block">
						<textarea placeholder="网站描述，一般不超过 200 字符" name="description" class="layui-textarea"><?php echo $options_settings_site['description']; ?></textarea>
					</div>
				</div>

				<div class="layui-form-item layui-form-text">
					<label class="layui-form-label">自定义 Header</label>
					<div class="layui-input-block">
						<textarea name="custom_header" placeholder="您可以自定义 <header>...</header> 之间的内容，如果您不理解用途，请勿填写！" rows="8" class="layui-textarea"><?php echo $options_settings_site['custom_header']; ?></textarea>
					</div>
				</div>

				<div class="layui-form-item layui-form-text">
					<label class="layui-form-label">自定义 Footer「订阅功能」</label>
					<div class="layui-input-block">
						<textarea name="custom_footer" placeholder="订阅可用，未订阅请留空。您可以自定义 <footer>...</footer> 之间的内容。" rows="8" class="layui-textarea"><?php echo $options_settings_site['custom_footer']; ?></textarea>
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="set_site">保存设置</button>
				</div>

			</form>
		</div>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	layui.use(['form'], function() {
		// 设置网站设置选项
		layui.form.on('submit(set_site)', function(data) {
			const loading_msg = layer.load(2, {time: 10 * 1000});
			if (data.field.custom_footer !== '') {
				layer.msg('正在验证订阅状态，最多等待 10 秒 ...', {icon: 4});
			}
			$.post('./index.php?c=Option&page=SetSite', data.field, function(data, status) {
				layer.close(loading_msg);
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

<?php exit(); ?>
