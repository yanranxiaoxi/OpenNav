<div class="layui-side layui-bg-black">
	<div class="layui-side-scroll">

		<ul class="layui-nav layui-nav-tree" lay-filter="navbar">
			<li class="layui-nav-item layui-nav-itemed">
				<a href="javascript:;">分类管理</a>
				<dl class="layui-nav-child">
					<dd><a href="./index.php?c=Admin&page=Categories">分类列表</a></dd>
					<dd><a href="./index.php?c=Admin&page=AddCategory">添加分类</a></dd>
				</dl>
			</li>
		</ul>

		<ul class="layui-nav layui-nav-tree" lay-filter="navbar">
			<li class="layui-nav-item layui-nav-itemed">
				<a href="javascript:;">链接管理</a>
				<dl class="layui-nav-child">
					<dd><a href="./index.php?c=Admin&page=Links">链接列表</a></dd>
					<dd><a href="./index.php?c=Admin&page=AddLink">添加链接</a></dd>
					<dd><a href="./index.php?c=Admin&page=ImportLinks">书签导入</a></dd>
				</dl>
			</li>
		</ul>

		<ul class="layui-nav layui-nav-tree" lay-filter="navbar">
			<li class="layui-nav-item layui-nav-itemed">
				<a href="javascript:;">系统设置</a>
				<dl class="layui-nav-child">
					<dd><a href="./index.php?c=Admin&page=Site">站点设置</a></dd>
					<!-- #TODO# --><!-- <dd><a href="./index.php?c=Admin&page=Theme">主题设置</a></dd> -->
					<dd><a href="./index.php?c=Admin&page=TransitionPage">过渡页面</a></dd>
					<dd><a href="./index.php?c=Admin&page=Subscribe">订阅设置</a></dd>
					<!-- #TODO# --><!-- <dd><a href="./index.php?c=Admin&page=GetAPI">获取 API</a></dd> -->
				</dl>
			</li>
		</ul>

		<ul class="layui-nav layui-nav-tree" lay-filter="navbar">
			<li class="layui-nav-item layui-nav-itemed">
				<a href="javascript:;">安全设置</a>
				<dl class="layui-nav-child">
					<dd><a href="./index.php?c=User&page=Options">用户设置</a></dd>
					<dd><a href="./index.php?c=TimeBaseValidator&page=Setup">时基验证</a></dd>
				</dl>
			</li>
		</ul>

	</div>
</div>
