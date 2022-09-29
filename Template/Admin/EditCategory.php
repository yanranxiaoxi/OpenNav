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

				<div class="layui-form-item" style="display: none;">
					<label class="layui-form-label">分类 ID</label>
					<div class="layui-input-block">
						<input type="number" name="id" required lay-verify="required|number" readonly="readonly" value="<?php echo $category_value['id']; ?>" placeholder="请输入分类 ID" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">分类标题</label>
					<div class="layui-input-block">
						<input type="text" name="title" required lay-verify="required" value="<?php echo $category_value['title']; ?>" placeholder="请输入分类名称" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">父级分类</label>
					<div class="layui-input-block">
						<select name="fid" lay-verify="required" lay-search>
							<!-- 显示上级分类，如果没有，则显示空 -->
							<?php if ($category_value['fid'] === 0) { ?>
							<!-- 如果上级分类名称为空 -->
							<option value="0">无</option>
							<?php } else { ?>
							<option value="<?php echo $category_value['fid']; ?>"><?php echo '[' . $category_value['fid'] . '] ' . $category_value['ftitle']; ?></option>
							<option value="0">无</option>
							<?php } ?>
							<!-- 显示上级分类 END -->

							<!-- 遍历所有一级分类 -->
							<?php
							// 分类的父分类不能为自己，也不能为上面已出现的上级分类
							foreach ($parent_categories as $parent_category) {
								if ($parent_category['id'] === $category_value['id'] || $parent_category['id'] === $category_value['fid']) {
									continue;
								}
							?>
							<option value="<?php echo $parent_category['id']; ?>"><?php echo '[' . $parent_category['id'] . '] ' . $parent_category['title']; ?></option>
							<?php } ?>
							<!-- 遍历所有一级分类 END -->
						</select> 
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">字体图标</label>
					<div class="layui-input-block">
						<input type="text" name="font_icon" id="iconHhys2" value="<?php echo $category_value['font_icon']; ?>" required lay-verify="required" lay-filter="iconHhys2" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">权重</label>
					<div class="layui-input-block">
						<input type="number" name="weight" id="weight" min="0" max="999" value="<?php echo $category_value['weight']; ?>" required lay-verify="required|number" placeholder="权重越高，排名越靠前，范围为 0-999" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">是否私有</label>
					<div class="layui-input-block">
						<input type="checkbox" name="property" value="1" <?php echo $category_value['property'] ? 'checked' : ''; ?> lay-skin="switch" lay-text="是|否" />
					</div>
				</div>

				<div class="layui-form-item layui-form-text">
					<label class="layui-form-label">描述</label>
					<div class="layui-input-block">
						<textarea name="description" placeholder="请输入描述内容" class="layui-textarea"><?php echo $category_value['description']; ?></textarea>
					</div>
				</div>

				<div class="layui-form-item">
						<button class="layui-btn" lay-submit lay-filter="edit_category">更新</button>
						<button type="reset" class="layui-btn layui-btn-primary">重置</button>
						<button class="layui-btn layui-btn-primary" lay-submit lay-filter="go_back">返回</button>
				</div>

			</form>
		</div>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
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
			value:'<?php echo $category_value['font_icon']; ?>' // 默认值
		});

		// 修改分类
		layui.form.on('submit(edit_category)', function(data) {
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
			$.post('./index.php?c=API&page=EditCategory', data.field, function(data, status) {
				// 如果修改成功
				if (data.code === 200) {
					layer.msg('修改成功！', {icon: 1});
				} else {
					layer.msg(data.message, {icon: 5});
				}
			});
			// console.log(data.field); // 当前容器的全部表单字段，名值对形式：{name: value}
			return false; // 阻止表单跳转
		});

		layui.form.on('submit(go_back)', function(data) {
			window.location.href = './index.php?c=Admin&page=Categories';
			return false;
		});
	})
</script>
