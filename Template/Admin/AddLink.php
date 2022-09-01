<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg">
				<p>1. 权重越大，排序越靠前</p>
				<p>2. 所属分类选择支持搜索</p>
				<p>3. 识别功能可以自动获取链接描述信息</p>
			</div>
		</div>
		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label">链接标题</label>
					<div class="layui-input-block">
						<input type="text" id="title" name="title" required lay-verify="required" placeholder="请输入链接名称" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">所属分类</label>
					<div class="layui-input-block">
						<select name="fid" lay-verify="required" lay-search>
							<option value=""></option>
							<?php foreach ($categorys as $category) { ?>
							<option value="<?php echo $category['id'] ?>"><?php echo $category['title']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">URL</label>
					<div class="layui-input-block">
						<input type="url" id="url" name="url" required lay-verify="required|url" placeholder="请输入有效链接，包含 http:// 标头" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">外部等待页</label>
					<div class="layui-input-block">
						<input type="url" id="url_standby" name="url_standby" placeholder="请输入外部等待页链接，如不需要请留空" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">权重</label>
					<div class="layui-input-block">
						<input type="number" name="weight" min="0" max="999" value="0" required lay-verify="required|number" placeholder="权重越高，排名越靠前，范围为 0-999" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">是否私有</label>
					<div class="layui-input-block">
						<input type="checkbox" name="property" value="1" lay-skin="switch" lay-text="是|否" />
					</div>
				</div>

				<div class="layui-form-item layui-form-text">
					<label class="layui-form-label">描述</label>
					<div class="layui-input-block">
						<textarea name="description" id="description" placeholder="请输入描述内容" class="layui-textarea"></textarea>
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="add_link">添加</button>
					<button class="layui-btn" lay-submit lay-filter="get_link_info">识别</button>
					<button type="reset" id="reset" class="layui-btn layui-btn-primary">重置</button>
				</div>

			</form>
		</div>
		<!-- 内容主体区域 END -->
	</div>
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	layui.use(['form'], function() {
		// 添加链接
		layui.form.on('submit(add_link)', function(data) {
			// 校验数据
			if (data.field.weight < 0) {
				layer.msg('权重范围为 0-999，已自动修正！', {icon: 5});
				$('#weight').val(0);
				return false;
			} else if (data.field.weight > 999) {
				layer.msg('权重范围为 0-999，已自动修正！', {icon: 5});
				$('#weight').val(999);
				return false;
			}
			$.post('./index.php?c=API&page=AddLink', data.field, function(data, status) {
				// 如果添加成功
				if (data.code === 200) {
					layer.msg('修改成功！', {icon: 1});
					$('#reset').click();
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});

		// 获取链接描述信息
		layui.form.on('submit(get_link_info)', function(data) {
			$.post('./index.php?c=API&page=GetLinkInfo', data.field, function(data, status) {
				// 如果识别成功
				if(data.code === 200) {
					$('#description').val(data.data);
					layer.msg('识别成功！', {icon: 1});
				}
				else{
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});
	})
</script>
