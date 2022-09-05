<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder" style="padding-bottom: 3em;">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg">
				<ol>
					<li>您可以前往 <a href="https://opennav.soraharu.com/license/" rel="nofollow" target="_blank" title="订阅授权服务">https://opennav.soraharu.com/license/</a> 订阅授权服务，订阅后可以：</li>
					<li>1. 享受一键更新 OpenNav</li>
					<li>2. 可在线下载和更新主题</li>
					<li>3. 可帮助 OpenNav 持续发展，让 OpenNav 变得更加美好</li>
					<li>4. 更多高级功能（自定义版权、广告管理等）</li>
				</ol>
			</div>
		</div>
		<!-- 说明提示框 END -->
		<!-- 订阅表格 -->
		<div class="layui-col-lg6">
			<h2 style = "margin-bottom: 1em;">我的订阅</h2>
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label">授权码</label>
					<div class="layui-input-block">
						<input type="text" id="license_key" name="license_key" value="<?php echo $options_settings_subscribe['license_key']; ?>" required lay-verify="required" autocomplete="off" placeholder="请输入授权码" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">授权邮箱</label>
					<div class="layui-input-block">
						<input type="email" name="email" id="email" value="<?php echo $options_settings_subscribe['email']; ?>" required lay-verify="required|email" autocomplete="off" placeholder="请输入授权邮箱" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item" style="display: none;">
					<label class="layui-form-label">域名</label>
					<div class="layui-input-block">
						<input type="text" name="domain" id="domain" readonly="readonly" value="<?php echo $_SERVER['HTTP_HOST']; ?>" autocomplete="off" placeholder="网站域名" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">到期时间</label>
					<div class="layui-input-block">
					<input type="text" name="end_time" id="end_time" readonly="readonly" value="<?php echo $subscribe_end_time; ?>" autocomplete="off" placeholder="授权到期时间" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="set_subscribe">保存设置</button>
					<button class="layui-btn layui-btn-primary layui-border-red" lay-submit lay-filter="delete_subscribe">删除授权</button>
					<a class="layui-btn layui-btn-primary layui-border-green" rel="nofollow" target="_blank" title="订阅授权" href="https://opennav.soraharu.com/license/"><i class="fa fa-shopping-cart"></i> 订阅授权</a>
				</div>

			</form>
		</div>
		<!-- 订阅表格 END -->
	</div>
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	layui.use(['form'], function() {
		// 设置订阅选项
		layui.form.on('submit(set_subscribe)', function(data) {
			const loading_msg = layer.load(2, {time: 20 * 1000});
			layer.msg('正在验证订阅状态，最多等待 20 秒 ...', {icon: 4});
			$.post('./index.php?c=Option&page=SetSubscribe', data.field, function(data, status) {
				layer.close(loading_msg);
				// 如果设置成功
				if (data.code === 200) {
					$('#end_time').val(data.data.end_time);
					layer.msg('设置成功！', {icon: 1});
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});

		// 删除订阅选项
		layui.form.on('submit(delete_subscribe)', function(data) {
			$.post('./index.php?c=Option&page=DeleteSubscribe', data.field, function(data, status) {
				// 如果删除成功
				if (data.code === 200) {
					$('#license_key').val('');
					$('#email').val('');
					$('#end_time').val('未授权');
					layer.msg('删除成功！', {icon: 1});
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});
	})
</script>
