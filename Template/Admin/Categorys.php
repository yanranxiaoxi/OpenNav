<?php require_once('../Template/Admin/Header.php'); ?>
<?php require_once('../Template/Admin/Left.php'); ?>

<div class="layui-body">
	<!-- 内容主体区域 -->
	<div class="layui-row content-body place-holder">
		<a class="layui-btn layui-btn-primary layui-border-green" target="_self" title="添加分类" href="./index.php?c=Admin&page=AddCategory"><i class="fa fa-plus-circle"></i> 添加分类</a>
		<div class="layui-col-lg12">
			<table id="categorys" lay-filter="categorys"></table>
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
			elem: '#categorys', // 指定原始 table 容器的选择器或 DOM
			title: 'OpenNav 分类列表', // 大标题（在文件导出等地方会用到）
			url: 'index.php?c=API&page=Categorys', // 数据接口
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
				{field: 'fid', title: '父级分类', minWidth: 60, sort: true, templet: function(data) {
					if (data.fid !== 0) {
						return '[' + data.fid + '] ' + data.ftitle;
					} else {
						return '无';
					}
				}},
				{field: 'title', title: '分类标题', minWidth: 100, sort: true},
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

		layui.table.on('tool(categorys)', function(obj) {
			if (obj.event === 'delete') {
			layer.confirm('确认要删除该分类吗？', {icon: 3, title:'提示'}, function(index) {
				$.post('./index.php?c=API&page=DeleteCategory', {'id': obj.data.id}, function(data, status) {
					if (data.code === 200) {
						obj.del();
					} else {
						layer.msg(data.message);
					}
				});
				layer.close(index);
			});
			} else if (obj.event === 'edit') {
				window.location.href = './index.php?c=Admin&page=EditCategory&id=' + obj.data.id;
			}
		});
	})
</script>
