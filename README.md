# OpenNav

⭐ 开放、自由的个人网络收藏夹 ⭐

[![pipeline status](https://gitlab.soraharu.com/XiaoXi/OpenNav/badges/main/pipeline.svg)](https://gitlab.soraharu.com/XiaoXi/OpenNav/-/commits/main) [![Latest Release](https://gitlab.soraharu.com/XiaoXi/OpenNav/-/badges/release.svg)](https://gitlab.soraharu.com/XiaoXi/OpenNav/-/releases)

🔗 [GitLab (Homepage)](https://gitlab.soraharu.com/XiaoXi/OpenNav) | 🔗 [GitHub](https://github.com/yanranxiaoxi/OpenNav)

## ✨ 特性

- 🔎 前端实时全文搜索
- 📁 两级分类标签设计
- 🌲 多前端主题一键切换
- 🖥️ 直观设计的控制台面板
- ⏱️ 支持高安全性时基验证登录
- ⭐ 一键导入 Chrome / Edge 收藏夹
- 🛠 全量操作 API 化，便捷二次开发
- ☁️ 轻量化开发，全接口注释
- 🔐 全局完整鉴权流程及渗透防护

## ✔️ 要求

- PHP 7.2 或更高版本
- PHP 插件
  - PHP [pdo_sqlite](https://www.php.net/manual/ref.pdo-sqlite.php) 拓展以使用 SQLite 数据库
  - PHP [curl](https://www.php.net/manual/book.curl.php) 拓展以获取在线内容（如自动更新、主题）
  - PHP [mbstring](https://www.php.net/manual/book.mbstring.php) 拓展以生成文字网站图标及配置时基验证
  - PHP [fileinfo](https://www.php.net/manual/book.fileinfo.php) 拓展以在线获取网站图标（可选）
  - PHP file_uploads 启用了上传机制以导入 Chrome /Edge 收藏夹
  - PHP upload_max_filesize 与最大文件上传大小匹配，建议至少为 8 MiB
- 支持所有 **现代浏览器**（极为有限的 Internet Explorer 支持）

## ⚙️ 部署

1. 前往本项目发布页 [GitLab](https://gitlab.soraharu.com/XiaoXi/OpenNav/-/releases) | [GitHub](https://github.com/yanranxiaoxi/OpenNav/releases) 获取最新编译版本软件包，包名为 `OpenNav-compiled.zip`
2. 将软件包上传至服务器站点目录中，并解压
3. 设置站点运行目录为解压出的 `Public` 目录
4. 访问你的网站，如果一切正常，将会显示 **安装 OpenNav** 页面

## 📜 开源许可

本项目支持个人及非商业社会团体免费使用大部分功能（除少数自定义功能及去除版权显示）。

个人（支持个人商用）及非商业社会团体可支付 **[99 CNY]** 获取永久使用授权。

商业用途企业需支付 **[299 CNY]** 以获取永久使用授权。

基于 [Mozilla Public License Version 2.0](https://choosealicense.com/licenses/mpl-2.0/) 许可进行开源，但包含以下附加条款：

1. 未获取永久使用授权的个人、团体、企业 **不属于** 许可的开源对象；
2. 对本项目进行修改、重构、衍生开发等再演绎过程时不可去除任何授权限制代码；
3. 本项目仅为通用个人网络收藏夹用途，其功能类似于浏览器的同步收藏夹，项目所有者及贡献者均不提供任何网络服务用于托管本项目的副本，所有未合并入主分支的代码均不属于本项目，所有用户自部署实例上的内容均与本项目无任何关联；
4. 处于任何国家、地区的任何个人、团体、企业在使用本项目及其任何衍生功能时，均需遵守中华人民共和国现行法律之约束，不将本项目的任何副本用于违法用途，所有不当用途所造成的合规风险均由使用者承担；
5. 所有参与本项目的贡献者需了解，如因合规原因需要更改本项目许可的附加条款甚至完整许可协议，项目所有者承诺将会发送电子邮件通知，并视为贡献者无条件同意此更改，无论是否接收到该电子邮件；
6. 参与贡献的开发者均可以申请获取使用授权，但仍受上述协议限制，如不接受以上许可协议及附加条件，请不要使用本项目或参与本项目的贡献。

以上附加条款仅为本项目所有者面对所在地域当前开源环境的无奈之举，请大家见谅，望有朝一日能安心去除以上附件条款，而无需有任何「开源负担」。
