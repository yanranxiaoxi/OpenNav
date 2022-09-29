<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg">
				<p>1. 权重越大，排序越靠前</p>
				<p>2. 父级分类选择支持搜索</p>
			</div>
		</div>
		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<div class="layui-form-item">
					<label class="layui-form-label">分类标题</label>
					<div class="layui-input-block">
						<input type="text" name="title" required lay-verify="required" placeholder="请输入分类名称" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">父级分类</label>
					<div class="layui-input-block">
						<select name="fid" lay-verify="required" lay-search>
							<option value="0">无</option>
							<?php foreach ($parent_categories as $parent_category) { ?>
								<option value="<?php echo $parent_category['id']; ?>"><?php echo '[' . $parent_category['id'] . '] ' . htmlspecialchars_decode($parent_category['title']); ?></option>
							<?php } ?>
						</select> 
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">字体图标</label>
					<div class="layui-input-block">
						<input type="text" name="font_icon" id="iconHhys2" value="fa-bookmark-o" required lay-verify="required" lay-filter="iconHhys2" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">权重</label>
					<div class="layui-input-block">
						<input type="number" name="weight" id="weight" min="0" max="999" value="0" required lay-verify="required|number" placeholder="权重越高，排名越靠前，范围为 0-999" autocomplete="off" class="layui-input" />
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
						<textarea name="description" placeholder="请输入描述内容" class="layui-textarea"></textarea>
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="add_category">添加</button>
					<button type="reset" id="reset" class="layui-btn layui-btn-primary">重置</button>
				</div>

			</form>
		</div>
	</div>
	<!-- 内容主题区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script>
	layui.config({
		base: './assets/'
	}).extend({
		iconHhysFa: 'iconHhys/iconHhysFa'
	});
	// 参考：https://gitee.com/luckygyl/iconFonts
	layui.use(['iconHhysFa', 'form'], function() {
		layui.iconHhysFa.render({
			elem: '#iconHhys2', // 选择器
			type: 'awesome', // 数据类型：fontClass / awesome，推荐使用 fontClass
			search: true, // 是否开启搜索
			url: './node_modules/font-awesome/less/variables.less', // fa 图标接口
			page: true, // 是否开启分页
			limit: 16, // 每页显示数量，默认 12
			value:'fa-bookmark-o' // 默认值
		});

		// 添加分类
		layui.form.on('submit(add_category)', function(data) {
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
			$.post('./index.php?c=API&page=AddCategory', data.field, function(data, status) {
				// 如果添加成功
				if (data.code === 200) {
					layer.msg('添加成功！', {icon: 1});
					$('#reset').click();
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});
	})
</script>
