<?php require_once __DIR__ . '/../../../Public/index.php'; ?>
<?php require_once __DIR__ . '/../../../Controller/Admin.php'; ?>
<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg"><!-- #TODO# -->
				过渡页配置方式，请参考：<a href="#" target="_blank" title="过渡页设置文档">过渡页面 - OpenNav 文档</a>
			</div>
		</div>
		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label" style="width: 130px;">功能开关</label>
					<div class="layui-input-block">
						<input type="checkbox" name="control" value="1" lay-skin="switch" <?php echo $options_settings_transition_page['control'] ? 'checked' : ''; ?> lay-text="开|关" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label" style="width: 130px;">访客停留时间</label>
					<div class="layui-input-inline">
						<input type="number" min="0" max="60" required lay-verify="required|number" name="visitor_stay_time" value="<?php echo $options_settings_transition_page['visitor_stay_time']; ?>" autocomplete="off" placeholder="访客停留时间，单位：秒" class="layui-input" />
					</div>
					<div class="layui-form-mid layui-word-aux">访客停留时间，单位秒</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label" style="width: 130px;">管理员停留时间</label>
					<div class="layui-input-inline">
						<input type="number" min="0" max="60" required lay-verify="required|number" name="admin_stay_time" value="<?php echo $options_settings_transition_page['admin_stay_time']; ?>" autocomplete="off" placeholder="管理员停留时间，单位：秒" class="layui-input" />
					</div>
					<div class="layui-form-mid layui-word-aux">管理员停留时间，单位秒</div>
				</div>

				<div class="layui-form-item layui-form-text">
					<label class="layui-form-label">过渡页菜单「订阅功能」</label>
					<div class="layui-input-block">
						<textarea name="menu" placeholder="支持 HTML 格式" rows="4" class="layui-textarea"><?php echo $options_settings_transition_page['menu']; ?></textarea>
					</div>
				</div>

				<div class="layui-form-item layui-form-text">
					<label class="layui-form-label">顶部广告「订阅功能」</label>
					<div class="layui-input-block">
						<textarea name="ad_top" placeholder="支持 HTML 格式" rows="2" class="layui-textarea"><?php echo $options_settings_transition_page['ad_top']; ?></textarea>
					</div>
				</div>

				<div class="layui-form-item layui-form-text">
					<label class="layui-form-label">底部广告「订阅功能」</label>
					<div class="layui-input-block">
						<textarea name="ad_bottom" placeholder="支持 HTML 格式" rows="2" class="layui-textarea"><?php echo $options_settings_transition_page['ad_bottom']; ?></textarea>
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="set_transition_page">保存设置</button>
				</div>

			</form>
		</div>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	layui.use(['form'], function() {
		// 设置过渡页面选项
		layui.form.on('submit(set_transition_page)', function(data) {
			const loading_msg = layer.load(2, {time: 10 * 1000});
			if (data.field.menu !== '' || data.field.ad_top !== '' || data.field.ad_bottom !== '') {
				layer.msg('正在验证订阅状态，最多等待 10 秒 ...', {icon: 4});
			}
			$.post('./index.php?c=Option&page=SetTransitionPage', data.field, function(data, status) {
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
