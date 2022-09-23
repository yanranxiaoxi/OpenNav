<?php
/**
 * OpenNav 全局配置文件
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */


/**
 * 用户初始化设置
 */
// 用户名
define('USERNAME', '{username}');
// 密码
define('PASSWORD', '{password}');
// 电子邮箱
define('EMAIL', '{email}');


/**
 * 数据库设置
 */
// 数据库类型，当前支持 SQLite、MySQL、MariaDB
define('DATABASE_TYPE', '{database_type}');
// 数据库主机名
define('DATABASE_HOST', '{database_host}');
// 数据库端口
define('DATABASE_PORT', '{database_port}');
// 数据库名
define('DATABASE_NAME', '{database_name}');
// 数据库用户名
define('DATABASE_USERNAME', '{database_username}');
// 数据库密码
define('DATABASE_PASSWORD', '{database_password}');
// 数据库前缀，在数据库类型为 SQLite 时无效
define('DATABASE_PREFIX', '{database_prefix}');


/**
 * 安全设置
 */
// 登录验证方式
// 3 = 用户名 + 密码；7 = 用户名 + 密码 + TOTP Code；
// 5 = (用户名 + TOTP Code) || (用户名 + 密码)；4 = TOTP Code || (用户名 + 密码)
define('LOGIN_AUTHENTICATION_MODE', 3);
// 强制 HTTPS 连接验证
// 启用此功能可以在进行高危操作（如登录）时强制 HTTPS 连接验证
define('ONLY_SECURE_CONNECTION', false);
// 允许通过 HTTP 执行周期任务
define('ALLOW_HTTP_CORN', true);


/**
 * 系统设置
 */
// Gravatar API 地址
define('GRAVATAR_API_URL', 'https://secure.gravatar.com');
// TOTP SecretKey
define('TOTP_SECRET_KEY', '{totp_secret_key}');
// COOKIE SecretKey
define('COOKIE_SECRET_KEY', '{cookie_secret_key}');
// 在线服务 API 地址
define('API_URL', 'https://opennav.soraharu.com/API/v1/');
// 检查更新 API 地址
define('CHECK_UPDATE_API_URL', 'https://gitlab.soraharu.com/api/v4/projects/90/releases');
// DEBUG 模式
define('DEBUG_MODE', false);
// 当前配置版本
define('CONFIG_VERSION', '0.1.4');
