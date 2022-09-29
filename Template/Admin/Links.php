<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder">
		<!-- 表单上方按钮 -->
		<!-- #TODO#
		<div class="lay-col-lg12">
			<form class="layui-form layui-form-pane">
				<div class="layui-form-item">
					<div class="layui-inline">
						<div class="layui-input-inline">
							<select name="fid" lay-search id="fid">
								<option value="">请选择一个分类</option>
								<?php foreach ($categorys as $category) { ?>
								<option value="<?php echo $category['id'] ?>">[<?php echo $category['id'] ?>] <?php echo $category['title']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="layui-input-inline" style="width: 100px;">
							<button class="layui-btn" lay-submit lay-filter="screen_link">查询此分类下的链接</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		-->
		<!-- 表单上方按钮 END -->
		<a class="layui-btn layui-btn-primary layui-border-green" target="_self" title="添加链接" href="./index.php?c=Admin&page=AddLink"><i class="fa fa-plus-circle"></i> 添加链接</a>
		<div class="layui-col-lg12">
			<table id="links" lay-filter="links" lay-data="{id: 'mylink_reload'}"></table>
			<!-- 表格头部工具栏 -->
			<!-- #TODO#
			<script type="text/html" id="linktool">
				<div class="layui-btn-container">
					<button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="getCheckData">删除选中</button>
					<button class="layui-btn layui-btn-sm" lay-event="readmoredata">批量修改分类</button>
					<button class="layui-btn layui-btn-sm" lay-event="set_private">设为私有</button>
					<button class="layui-btn layui-btn-sm" lay-event="set_public">设为公有</button>
				</div>
			</script>
			-->
			<!-- 表格头部工具栏 END -->
		</div>
		<script type="text/html" id="navbar_operate">
			<a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
			<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">删除</a>
		</script>
	</div>
	<!-- 内容主体区域 END -->
</div>

<?php require_once('../Template/Admin/Footer.php'); ?>

<script type="text/javascript">
	function timestampToDate(timestamp) {
		// 数据库中的 timestamp 仅保存到秒
		// Date 计算到微秒，相差 1000 倍
		const now = new Date(timestamp * 1000),
		y = now.getFullYear(),
		m = now.getMonth() + 1,
		d = now.getDate();
		return y + "-" + (m < 10 ? "0" + m : m) + "-" + (d < 10 ? "0" + d : d) + " " + now.toTimeString().substr(0, 8);
	}

	layui.use(['table'], function() {
		// 参考：https://www.layuiweb.com/doc/modules/table.html
		layui.table.render({
			elem: '#links', // 指定原始 table 容器的选择器或 DOM
			title: 'OpenNav 链接列表', // 大标题（在文件导出等地方会用到）
			url: 'index.php?c=API&page=Links', // 数据接口
			toolbar: true, // 开启工具栏
			defaultToolbar: ['filter', 'print', 'exports'], // 工具栏右侧图标
			page: true, // 开启分页
			limit: 10, // 默认每页数据量
			limits: [10, 25, 50, 100, 250, 500, 1000, 99999], // 可选每页数据量
			loading: true, // 显示加载条
			autoSort: true, // 前端自动排序
			initSort: { // 初始排序方式
				field: 'id', //排序字段，对应 cols 设定的各字段名
				type: 'asc' //排序方式  asc：升序、desc：降序、null：默认排序
			},
			even: true, // 隔行背景
			request: {
				pageName: 'pages', // 页码的参数名称，默认：page
				limitName: 'limit' // 每页数据量的参数名，默认：limit
			},
			response: {
				statusName: 'code', // 规定数据状态的字段名称，默认：code
				statusCode: 200, // 规定成功的状态码，默认：0
				msgName: 'message', // 规定状态信息的字段名称，默认：msg
				countName: 'count', // 规定数据总数的字段名称，默认：count
				dataName: 'data' // 规定数据列表的字段名称，默认：data
			},
			cols: [[ // 表头
				{field: 'id', title: 'ID', minWidth: 60, sort: true, fixed: 'left'},
				{field: 'fid', title: '所属分类', minWidth: 60, sort: true, templet: function(data) {
					return '[' + data.fid + '] ' + data.ftitle;
				}},
				{field: 'title', title: '链接标题', minWidth: 100, sort: true},
				{field: 'url', title: 'URL', minWidth: 140, templet: function(data) {
					return '<a href="' + data.url + '" target="_blank" title="' + data.title + '">' + data.url + '</a>';
				}},
				{field: 'url_standby', title: '外部等待页', minWidth: 100, templet: function(data) {
					return '<a href="' + data.url_standby + '" target="_blank">' + data.url_standby + '</a>';
				}},
				{field: 'add_time', title: '添加时间', sort: true, templet: function(data) {
					return timestampToDate(data.add_time);
				}},
				{field: 'update_time', title: '修改时间', templet: function(data) {
					if (data.update_time !== null) {
						return timestampToDate(data.update_time);
					} else {
						return '';
					}
				}},
				{field: 'weight', title: '权重', minWidth: 60, sort: true},
				{field: 'property', title: '是否私有', minWidth: 60, sort: true, templet: function(data) {
						if (data.property === 1) {
							return '是';
						} else {
							return '否';
						}
				}},
				{field: 'description', title: '描述'},
				{title:'操作', toolbar: '#navbar_operate', minWidth: 120, fixed: 'right'}
			]]
		});

		layui.table.on('tool(links)', function(obj) {
			if (obj.event === 'delete') {
			layer.confirm('确认要删除该链接吗？', {icon: 3, title:'提示'}, function(index) {
				$.post('./index.php?c=API&page=DeleteLink', {'id': obj.data.id}, function(data, status) {
					if (data.code === 200) {
						obj.del();
					} else {
						layer.msg(data.message);
					}
				});
				layer.close(index);
			});
			} else if (obj.event === 'edit') {
				window.location.href = './index.php?c=Admin&page=EditLink&id=' + obj.data.id;
			}
		});
	})
</script>
