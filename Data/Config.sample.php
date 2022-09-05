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
 * 安全设置
 */
// 登录验证方式
// 3 = 用户名 + 密码；7 = 用户名 + 密码 + TOTP Code；
// 5 = (用户名 + TOTP Code) || (用户名 + 密码)；4 = TOTP Code || (用户名 + 密码)
define('LOGIN_AUTHENTICATION_MODE', 3);
// 强制 HTTPS 连接验证
// 启用此功能可以在进行高危操作（如登录）时强制 HTTPS 连接验证
define('ONLY_SECURE_CONNECTION', false);


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
