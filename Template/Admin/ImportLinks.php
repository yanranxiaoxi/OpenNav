<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder">
		<!-- 说明提示框 -->
		<div class="layui-col-lg12">
			<div class="setting-msg">
				仅支持导入 <em>.html</em> 格式书签文件，导入时会自动创建不存在的分类
			</div>
		</div>
		<!-- 说明提示框 END -->
		<div class="layui-col-lg6">
			<form class="layui-form layui-form-pane">

				<!-- 上传 -->
				<div class="layui-form-item">
					<div class="layui-upload-drag" id="upload_html">
						<i class="layui-icon layui-icon-upload"></i>
						<p>点击上传，或将书签文件拖拽到此处</p>
						<div class="layui-hide" id="file">
							<hr />
							<img src="" alt="上传成功后渲染" style="max-width: 100%;">
						</div>
					</div>
				</div>
				<!-- 上传 END -->

				<div class="layui-form-item">
					<label class="layui-form-label">书签路径</label>
					<div class="layui-input-block">
						<input type="text" id="file_directory" name="file_directory" required lay-verify="required" placeholder="请输入书签路径" autocomplete="off" class="layui-input" />
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">是否私有</label>
					<div class="layui-input-block">
						<input type="checkbox" name="property" value="1" lay-skin="switch" lay-text="是|否" />
					</div>
				</div>

				<div class="layui-form-item">
					<button class="layui-btn" lay-submit lay-filter="import_links">开始导入</button>
				</div>

			</form>
		</div>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>
