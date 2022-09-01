<?php
/**
 * OpenNav 全局用户配置文件
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
// 邮箱，用于后台 Gravatar 头像显示
define('EMAIL', '{email}');
// TOTP SecretKey
define('TOTP_SECRET_KEY', '{totp_secret_key}');
// COOKIE SecretKey
define('COOKIE_SECRET_KEY', '{cookie_secret_key}');

// 定义在线服务 API 地址
// 你可以将 API_URL 定义为空以完全禁用在线服务
define('API_URL', 'https://opennav.soraharu.com');
