<?php
/**
 * OpenNav 全局 Helper 类
 * 
 * 「Logic Safety」和「With Auth」函数可以在任意位置调用
 * 「Auth Required」函数均带有「_AuthRequired」标记，仅允许在鉴权后调用
 * 
 * @author		XiaoXi <admin@soraharu.com>
 * @copyright	All rights reserved by XiaoXi
 * @license		Mozilla Public License 2.0
 * 
 * @link		https://opennav.soraharu.com/
 */

namespace OpenNav\Helper;

class GlobalHelper {
	private $database;
	public function __construct($database) {
		$this->database = $database;
		// 返回 JSON 类型
		// header('Content-Type: application/json; charset=utf-8');
	}

	/**
	 * 获取访客 IP 地址「Logic Safety」
	 * 
	 * @return string|null 访客 IP 地址
	 */
	private function getVisitorIP() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
						return $ip;
					}
				}
			}
		}
		return null;
	}

	/**
	 * 使用 PHP cURL 请求获取数据「Logic Safety」
	 * 
	 * @param	string	$url		预请求的 URL 地址
	 * @param	array	$post_array	需要 POST 发送的数据
	 * @param	int		$timeout	请求超时时间，以秒为单位
	 * 
	 * @return	string|false		获取到的数据，false 代表请求失败
	 */
	public function curlGet($url, $post_array = [], $timeout = 10) {
		$curl = curl_init($url);
		// 设置 UserAgent
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Safari/537.36');
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

		if (!empty($post_array)) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_array);
		}

		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
	}

	/**
	 * 获取登录状态「Logic Safety」
	 * 主要鉴权函数，可以在任意位置调用，并返回当前用户的登录状态
	 * 
	 * @return bool 登录状态
	 */
	public function isLogin() {
		$visitor_ip = $this->getVisitorIP();
		$visitor_infomation = ($visitor_ip === null) ? $_SERVER['HTTP_USER_AGENT'] : $visitor_ip;
		$local_key = USERNAME . COOKIE_SECRET_KEY . 'opennav' . $visitor_infomation;
		$local_key_hash = hash('sha256', $local_key);
		// 获取 Session Cookie
		$cookie_session_key = !empty($_COOKIE['opennav_session_key']) ? $_COOKIE['opennav_session_key'] : '';
		// 如果已经成功登录
		if ($cookie_session_key === $local_key_hash) {
			// 延长 Cookie 时间为 30 天
			if (ONLY_SECURE_CONNECTION === true) {
				// 仅 HTTPS 设置 Session Cookie
				setcookie('opennav_session_key', $local_key_hash, time() + 60 * 60 * 24 * 30, '/', null, true, true);
			} else {
				// 设置 Session Cookie
				setcookie('opennav_session_key', $local_key_hash, time() + 60 * 60 * 24 * 30, '/', null, false, true);
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 设置登录状态「Auth Required」
	 * 
	 * @return bool 设置状态
	 */
	public function setLogin_AuthRequired() {
		$visitor_ip = $this->getVisitorIP();
		$visitor_infomation = ($visitor_ip === null) ? $_SERVER['HTTP_USER_AGENT'] : $visitor_ip;
		$local_key = USERNAME . COOKIE_SECRET_KEY . 'opennav' . $visitor_infomation;
		$local_key_hash = hash('sha256', $local_key);
		if (ONLY_SECURE_CONNECTION === true) {
			// 仅 HTTPS 设置 Session Cookie
			return setcookie('opennav_session_key', $local_key_hash, time() + 60 * 60 * 24 * 30, '/', null, true, true);
		} else {
			// 设置 Session Cookie
			return setcookie('opennav_session_key', $local_key_hash, time() + 60 * 60 * 24 * 30, '/', null, false, true);
		}
	}

	/**
	 * 移除登录状态「Logic Safety」
	 * 
	 * @return bool 设置状态
	 */
	public function removeLogin() {
		// 设置 Session Cookie
		return setcookie('opennav_session_key', '', time() - 3600, '/', null, false, true);
	}

	/**
	 * 获取暗色模式状态「Logic Safety」
	 * 
	 * @return bool 暗色模式状态
	 */
	public function isDarkMode() {
		$cookie_theme_layout = isset($_COOKIE['opennav_theme_layout']) ? $_COOKIE['opennav_theme_layout'] : '';
		if ($cookie_theme_layout === 'dark') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取随机密钥「Logic Safety」
	 * 
	 * @param	int		$length	随机密钥长度
	 * @param	bool	$symbol	是否包含特殊字符
	 * 
	 * @return	string	随机密钥
	 */
	public function getRandomKey($length = 64, $symbol = false) {
		$charset = $symbol ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_[]{}<>~`+=,.;:/?|' : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$random_key = '';
		for ($i = 0; $i < $length; $i++) {
			$random_key .= $charset[mt_rand(0, strlen($charset) - 1)];
		}
		return $random_key;
	}

	/**
	 * 获取一级分类「With Auth」
	 * 
	 * @return array 一级分类二维数组
	 */
	public function getParentCategorys() {
		$parent_categorys = [];
		if ($this->isLogin()) {
			// 查询一级分类
			$parent_categorys = $this->database->select('on_categorys', '*', [
				'fid' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
		} else {
			// 查询一级分类
			$parent_categorys = $this->database->select('on_categorys', '*', [
				'fid' => 0,
				'property' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
		}
		return $parent_categorys;
	}

	/**
	 * 获取一级分类「Auth Required」
	 * 
	 * @return array 一级分类二维数组
	 */
	public function getParentCategorys_AuthRequired() {
		$parent_categorys = $this->database->select('on_categorys', '*', [
			'fid' => 0,
			'ORDER' => ['id' => 'ASC']
		]);
		return $parent_categorys;
	}

	/**
	 * 获取一级分类 [id, title]「Auth Required」
	 * 
	 * @return array 一级分类二维数组 [id, title]
	 */
	public function getParentCategorysIdTitle_AuthRequired() {
		$parent_categorys = $this->database->select('on_categorys', ['id', 'title'], [
			'fid' => 0,
			'ORDER' => ['id' => 'ASC']
		]);
		return $parent_categorys;
	}

	/**
	 * 获取指定一级分类的二级分类「With Auth」
	 * 
	 * @param	int		$parent_category_id	一级分类 ID
	 * 
	 * @return	array	二级分类二维数组
	 */
	public function getChildCategorysByParentCategoryId($parent_category_id) {
		$child_categorys = [];
		if ($this->isLogin()) {
			// 查询二级分类
			$child_categorys = $this->database->select('on_categorys', '*', [
				'fid' => $parent_category_id,
				'ORDER' => ['weight' => 'DESC']
			]);
		} else {
			// 查询二级分类
			$child_categorys = $this->database->select('on_categorys', '*', [
				'fid' => $parent_category_id,
				'property' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
		}
		return $child_categorys;
	}

	/**
	 * 获取分类「With Auth」
	 * 
	 * @return array 分类二维数组
	 */
	public function getCategorys() {
		$categorys = [];
		if ($this->isLogin()) {
			// 查询一级分类
			$category_parent_array = $this->database->select('on_categorys', '*', [
				'fid' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
			// 遍历一级分类
			foreach ($category_parent_array as $category_parent_value) {
				// 将一级分类追加到 $categorys 数组
				array_push($categorys, $category_parent_value);
				// 查询二级分类
				$category_child_array = $this->database->select('on_categorys', '*', [
					'fid' => $category_parent_value['id'],
					'ORDER' => ['weight' => 'DESC']
				]);
				// 遍历该一级分类下的所有子分类
				foreach ($category_child_array as $category_child_value) {
					// 将所有子分类追加到 $categorys 数组
					array_push($categorys, $category_child_value);
				}
			}
		} else {
			// 查询一级分类
			$category_parent_array = $this->database->select('on_categorys', '*', [
				'fid' => 0,
				'property' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
			// 遍历一级分类
			foreach ($category_parent_array as $category_parent_value) {
				// 将一级分类追加到 $categorys 数组
				array_push($categorys, $category_parent_value);
				// 查询二级分类
				$category_child_array = $this->database->select('on_categorys', '*', [
					'fid' => $category_parent_value['id'],
					'property' => 0,
					'ORDER' => ['weight' => 'DESC']
				]);
				// 遍历该一级分类下的所有子分类
				foreach ($category_child_array as $category_child_value) {
					// 将所有子分类追加到 $categorys 数组
					array_push($categorys, $category_child_value);
				}
			}
		}
		return $categorys;
	}

	/**
	 * 获取分类 [id, title]「Auth Required」
	 * 
	 * @return array 分类二维数组 [id, title]
	 */
	public function getCategorysIdTitle_AuthRequired() {
		$categorys = $this->database->select('on_categorys', ['id', 'title'], [
			'ORDER' => ['id' => 'ASC']
		]);
		return $categorys;
	}

	/**
	 * 获取指定分类 ID 的分类「With Auth」
	 * 
	 * @param	int		$category_id	分类 ID
	 * 
	 * @return	array	分类数组
	 */
	public function getCategoryByCategoryId($category_id) {
		$category_value = [];
		if ($this->isLogin()) {
			$category_value = $this->database->get('on_categorys', '*', [
				'id' => $category_id
			]);
		} else {
			$category_value = $this->database->get('on_categorys', '*', [
				'id' => $category_id,
				'property' => 0
			]);
		}
		return $category_value;
	}

	/**
	 * 获取指定分类 ID 的分类「Auth Required」
	 * 
	 * @param	int		$category_id	分类 ID
	 * 
	 * @return	array	分类数组
	 */
	public function getCategoryByCategoryId_AuthRequired($category_id) {
		$category_value = $this->database->get('on_categorys', '*', [
			'id' => $category_id
		]);
		return $category_value;
	}

	/**
	 * 获取指定分类 ID 的分类 title「Auth Required」
	 * 
	 * @param	int		$category_id	分类 ID
	 * 
	 * @return	string	分类 title
	 */
	public function getCategoryTitleByCategoryId_AuthRequired($category_id) {
		$category_value_title = $this->database->get('on_categorys', 'title', [
			'id' => $category_id
		]);
		return $category_value_title;
	}

	/**
	 * 获取指定分类的链接「With Auth」
	 * 
	 * @param	int		$category_id	分类 ID
	 * 
	 * @return	array	链接二维数组
	 */
	public function getLinksByCategoryId($category_id) {
		$links = [];
		if ($this->isLogin()) {
			$links = $this->database->select('on_links', '*', [
				'fid' => $category_id,
				'ORDER' => ['weight' => 'DESC']
			]);
		} else {
			$links = $this->database->select('on_links', '*', [
				'fid' => $category_id,
				'property' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
		}
		return $links;
	}

	/**
	 * 获取指定链接 ID 的链接「With Auth」
	 * 
	 * @param	int		$link_id	链接 ID
	 * 
	 * @return	array	链接数组
	 */
	public function getLinkByLinkId($link_id) {
		$link_value = [];
		if ($this->isLogin()) {
			$link_value = $this->database->get('on_links', '*', [
				'id' => $link_id
			]);
		} else {
			$link_value = $this->database->get('on_links', '*', [
				'id' => $link_id,
				'property' => 0
			]);
		}
		return $link_value;
	}

	/**
	 * 获取指定链接 ID 的链接「Auth Required」
	 * 
	 * @param	int		$link_id	链接 ID
	 * 
	 * @return	array	链接数组
	 */
	public function getLinkByLinkId_AuthRequired($link_id) {
		$link_value = $this->database->get('on_links', '*', [
			'id' => $link_id
		]);
		return $link_value;
	}

	/**
	 * 获取分页分类「Auth Required」
	 * 
	 * @todo	#TODO#	增加排序方式参数
	 * 
	 * @param	int		$pages	当前页数
	 * @param	int		$limit	每页行数
	 * 
	 * @return	array	分类二维数组
	 */
	public function getCategorysPagination_AuthRequired($pages = 0, $limit = 0) {
		$categorys = [];
		// 查询分类
		if ($pages > 0 && $limit > 0) {
			// 首行数据偏移量
			$offset = ($pages - 1) * $limit;
			$categorys = $this->database->select('on_categorys', '*', [
				'ORDER' => ['id' => 'ASC'],
				'LIMIT' => [$offset, $limit]
			]);
		} else {
			$categorys = $this->database->select('on_categorys', '*', [
				'ORDER' => ['id' => 'ASC']
			]);
		}
		return $categorys;
	}

	/**
	 * 获取分页链接「Auth Required」
	 * 
	 * @todo	#TODO#	增加排序方式参数
	 * 
	 * @param	int		$pages	当前页数
	 * @param	int		$limit	每页行数
	 * 
	 * @return	array	链接二维数组
	 */
	public function getLinksPagination_AuthRequired($pages = 0, $limit = 0) {
		$links = [];
		// 查询分类
		if ($pages > 0 && $limit > 0) {
			// 首行数据偏移量
			$offset = ($pages - 1) * $limit;
			$links = $this->database->select('on_links', '*', [
				'ORDER' => ['id' => 'ASC'],
				'LIMIT' => [$offset, $limit]
			]);
		} else {
			$links = $this->database->select('on_links', '*', [
				'ORDER' => ['id' => 'ASC']
			]);
		}
		return $links;
	}

	/**
	 * 添加分类「Auth Required」
	 * 
	 * @param	array	$category_data	分类数据：[fid, weight, title, font_icon, description, property]
	 * 
	 * @return	true|string				修改状态，失败时返回 string
	 */
	public function addCategory_AuthRequired($category_data) {
		if ($category_data['weight'] < 0 || $category_data['weight'] > 999) {
			return '权重范围为 0-999';
		}
		if ($category_data['property'] !== 0 && $category_data['property'] !== 1) {
			return '私有状态只能为是(1)或否(0)';
		}
		if (empty($category_data['title']) || empty($category_data['font_icon'])) {
			return '必填项不能为空！';
		}
		// 判断字符串型数据长度合法性
		if (strlen($category_data['title']) > 64) {
			return '标题长度不能超过 64 位（中文字符占 3 位）';
		}
		if (strlen($category_data['font_icon']) > 32) {
			return '字体图标长度错误！';
		}
		if (strlen($category_data['description']) > 256) {
			return '描述长度不能超过 256 位（中文字符占 3 位）';
		}
		// 当分类 fid 不为 0 时，查询分类 fid 是否为一级分类的 id
		if ($category_data['fid'] !== 0) {
			$parent_categorys = $this->database->select('on_categorys', 'id', [
				'fid' => 0
			]);
			// 如果不是一级分类的 id，则数据不合法
			if (!in_array($category_data['fid'], $parent_categorys)) {
				return '父级分类必须为一级分类！';
			} else {
				// 否则数据合法，写入数据库
				$category_data['add_time'] = time();
				$this->database->insert('on_categorys', $category_data);
				return true;
			}
		} else {
			// 否则数据合法，写入数据库
			$category_data['add_time'] = time();
			$this->database->insert('on_categorys', $category_data);
			return true;
		}
	}

	/**
	 * 添加链接「Auth Required」
	 * 
	 * @param	array	$link_data	链接数据：[fid, weight, title, url, url_standby, description, property]
	 * 
	 * @return	true|string			添加状态，失败时返回 string
	 */
	public function addLink_AuthRequired($link_data) {
		if ($link_data['weight'] < 0 || $link_data['weight'] > 999) {
			return '权重范围为 0-999';
		}
		if ($link_data['property'] !== 0 && $link_data['property'] !== 1) {
			return '私有状态只能为是(1)或否(0)';
		}
		if (empty($link_data['title']) || empty($link_data['url'])) {
			return '必填项不能为空！';
		}
		// 判断字符串型数据长度合法性
		if (strlen($link_data['title']) > 64) {
			return '标题长度不能超过 64 位（中文字符占 3 位）';
		}
		if (strlen($link_data['url']) > 1024) {
			return '链接长度不能超过 1024 位！';
		}
		if (strlen($link_data['url_standby']) > 1024) {
			return '外部等待页链接长度不能超过 1024 位！';
		}
		if (strlen($link_data['description']) > 256) {
			return '描述长度不能超过 256 位（中文字符占 3 位）';
		}
		$category = $this->database->get('on_categorys', 'id', [
			'id' => $link_data['fid']
		]);
		if (empty($category)) {
			return '所属分类不存在！';
		}
		// 数据合法，写入数据库
		$link_data['add_time'] = time();
		$this->database->insert('on_links', $link_data);
		return true;
	}

	/**
	 * 修改分类「Auth Required」
	 * 
	 * @param	int		$category_id	分类 ID
	 * @param	array	$category_data	分类数据：[fid, weight, title, font_icon, description, property]
	 * 
	 * @return	true|string				修改状态，失败时返回 string
	 */
	public function updateCategory_AuthRequired($category_id, $category_data) {
		if ($category_id === $category_data['fid']) {
			return '分类的 ID 与父级分类的 ID 不能相同！';
		}
		if ($category_data['weight'] < 0 || $category_data['weight'] > 999) {
			return '权重范围为 0-999';
		}
		if ($category_data['property'] !== 0 && $category_data['property'] !== 1) {
			return '私有状态只能为是(1)或否(0)';
		}
		if (empty($category_data['title']) || empty($category_data['font_icon'])) {
			return '必填项不能为空！';
		}
		// 判断字符串型数据长度合法性
		if (strlen($category_data['title']) > 64) {
			return '标题长度不能超过 64 位（中文字符占 3 位）';
		}
		if (strlen($category_data['font_icon']) > 32) {
			return '字体图标长度错误！';
		}
		if (strlen($category_data['description']) > 256) {
			return '描述长度不能超过 256 位（中文字符占 3 位）';
		}
		// 当分类 fid 不为 0 时，查询分类 fid 是否为一级分类的 id
		if ($category_data['fid'] !== 0) {
			$parent_categorys = $this->database->select('on_categorys', 'id', [
				'fid' => 0
			]);
			// 如果不是一级分类的 id，则数据不合法
			if (!in_array($category_data['fid'], $parent_categorys)) {
				return '父级分类必须为一级分类！';
			} else {
				// 否则数据合法，写入数据库
				$category_data['update_time'] = time();
				$this->database->update('on_categorys', $category_data, [
					'id' => $category_id
				]);
				return true;
			}
		} else {
			// 否则数据合法，写入数据库
			$category_data['update_time'] = time();
			$this->database->update('on_categorys', $category_data, [
				'id' => $category_id
			]);
			return true;
		}
	}

	/**
	 * 修改链接「Auth Required」
	 * 
	 * @param	int		$link_id	链接 ID
	 * @param	array	$link_data	链接数据：[fid, weight, title, url, url_standby, description, property]
	 * 
	 * @return	true|string			修改状态，失败时返回 string
	 */
	public function updateLink_AuthRequired($link_id, $link_data) {
		if ($link_data['weight'] < 0 || $link_data['weight'] > 999) {
			return '权重范围为 0-999';
		}
		if ($link_data['property'] !== 0 && $link_data['property'] !== 1) {
			return '私有状态只能为是(1)或否(0)';
		}
		if (empty($link_data['title']) || empty($link_data['url'])) {
			return '必填项不能为空！';
		}
		// 判断字符串型数据长度合法性
		if (strlen($link_data['title']) > 64) {
			return '标题长度不能超过 64 位（中文字符占 3 位）';
		}
		if (strlen($link_data['url']) > 1024) {
			return '链接长度不能超过 1024 位！';
		}
		if (strlen($link_data['url_standby']) > 1024) {
			return '外部等待页链接长度不能超过 1024 位！';
		}
		if (strlen($link_data['description']) > 256) {
			return '描述长度不能超过 256 位（中文字符占 3 位）';
		}
		$category = $this->database->get('on_categorys', 'id', [
			'id' => $link_data['fid']
		]);
		if (empty($category)) {
			return '所属分类不存在！';
		}
		// 数据合法，写入数据库
		$link_data['update_time'] = time();
		$this->database->update('on_links', $link_data, [
			'id' => $link_id
		]);
		return true;
	}

	/**
	 * 删除分类「Auth Required」
	 * 
	 * @param	int		$category_id	分类 ID
	 * 
	 * @return	bool	删除状态
	 */
	public function deleteCategory_AuthRequired($category_id) {
		$category_value = $this->database->get('on_categorys', 'fid', [
			'id' => $category_id
		]);
		$child_categorys = null;
		if ($category_value['fid'] === 0) {
			$child_categorys = $this->database->select('on_categorys', 'id', [
				'fid' => $category_id
			]);
		}
		$links = $this->database->select('on_links', 'id', [
			'fid' => $category_id
		]);
		if (empty($child_categorys) && empty($links)) {
			$this->database->delete('on_categorys', [
				'id' => $category_id
			]);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 删除链接「Auth Required」
	 * 
	 * @param int $link_id 链接 ID
	 */
	public function deleteLink_AuthRequired($link_id) {
		$this->database->delete('on_links', [
			'id' => $link_id
		]);
	}

	/**
	 * 设置指定链接的点击数「Logic Safety」
	 * 
	 * @param	int		$link_id	链接 ID
	 * @param	string	$mode		设置模式：add = 增加；subtract = 减少；zero = 归零
	 * @param	int		$count		增加或减少的数量
	 * 
	 * @return	bool	设置状态
	 */
	public function setLinkValueClick($link_id, $mode = 'add', $count = 1) {
		$link_value_click = $this->database->get('on_links', 'click', [
			'id' => $link_id
		]);
		if ($mode === 'add' || $mode === 'subtract' || $mode === 'zero') {
			if ($mode === 'add') {
				$link_value_click = $link_value_click + $count;
			} elseif ($mode === 'subtract') {
				$link_value_click = $link_value_click - $count;
			} else {
				$link_value_click = 0;
			}
			$this->database->update('on_links', [
				'click' => $link_value_click
			], [
				'id' => $link_id
			]);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取主题选项「Logic Safety」
	 * 
	 * @return string 主题
	 */
	public function getOptionsTheme() {
		$options_theme = $this->database->get('on_options', 'value', [
			'key' => 'theme'
		]);
		return $options_theme;
	}

	/**
	 * 修改主题选项「Auth Required」
	 * 
	 * @param string $options_theme 主题
	 */
	public function setOptionsTheme_AuthRequired($options_theme) {
		$options_theme = $this->database->update('on_options', [
			'value' => $options_theme
		], [
			'key' => 'theme'
		]);
	}

	/**
	 * 获取站点设置选项「Logic Safety」
	 * 
	 * @return array 站点设置
	 */
	public function getOptionsSettingsSite() {
		$options_settings_site = $this->database->get('on_options', 'value', [
			'key' => 'settings_site'
		]);
		$options_settings_site = unserialize($options_settings_site);
		return $options_settings_site;
	}

	/**
	 * 修改站点设置选项「Auth Required」
	 * 
	 * @param array $options_settings_site 站点设置
	 */
	public function setOptionsSettingsSite_AuthRequired($options_settings_site) {
		$options_settings_site = serialize($options_settings_site);
		$options_settings_site = $this->database->update('on_options', [
			'value' => $options_settings_site
		], [
			'key' => 'settings_site'
		]);
	}

	/**
	 * 获取过渡页设置选项「Logic Safety」
	 * 
	 * @return array 过渡页设置
	 */
	public function getOptionsSettingsTransitionPage() {
		$options_settings_transition_page = $this->database->get('on_options', 'value', [
			'key' => 'settings_transition_page'
		]);
		$options_settings_transition_page = unserialize($options_settings_transition_page);
		return $options_settings_transition_page;
	}

	/**
	 * 修改过渡页设置选项「Auth Required」
	 * 
	 * @param array $options_settings_transition_page 过渡页设置
	 */
	public function setOptionsSettingsTransitionPage_AuthRequired($options_settings_transition_page) {
		$options_settings_transition_page = serialize($options_settings_transition_page);
		$options_settings_transition_page = $this->database->update('on_options', [
			'value' => $options_settings_transition_page
		], [
			'key' => 'settings_transition_page'
		]);
	}

	/**
	 * 获取订阅设置选项「Auth Required」
	 * 
	 * @return array 订阅设置
	 */
	public function getOptionsSettingsSubscribe_AuthRequired() {
		$options_settings_subscribe = $this->database->get('on_options', 'value', [
			'key' => 'settings_subscribe'
		]);
		$options_settings_subscribe = unserialize($options_settings_subscribe);
		return $options_settings_subscribe;
	}

	/**
	 * 修改订阅设置选项「Auth Required」
	 * 
	 * @param array $options_settings_subscribe 订阅设置
	 */
	public function setOptionsSettingsSubscribe_AuthRequired($options_settings_subscribe) {
		$options_settings_subscribe = serialize($options_settings_subscribe);
		$options_settings_subscribe = $this->database->update('on_options', [
			'value' => $options_settings_subscribe
		], [
			'key' => 'settings_subscribe'
		]);
	}

	/**
	 * 获取订阅状态「Logic Safety」
	 * 
	 * @return bool 订阅状态
	 */
	public function isSubscribe() {
		// 获取选项数组
		$options_settings_subscribe = $this->getOptionsSettingsSubscribe_AuthRequired();
		// 处理 domain 变量并存入选项数组
		$domain_array = explode(':', htmlspecialchars(trim($_SERVER['HTTP_HOST'])));
		$options_settings_subscribe['domain'] = $domain_array[0];
		// 请求查询接口返回数据
		$curl_subscribe_data = $this->curlGet(API_URL . 'CheckSubscribe.php', $options_settings_subscribe);
		// 如果请求到了数据
		if ($curl_subscribe_data !== false) {
			// 解码请求到的数据
			$curl_subscribe_data = json_decode($curl_subscribe_data, true);
			if ($curl_subscribe_data['code'] === 200) {
				// 将请求到的 end_time 存入选项数组
				$options_settings_subscribe['end_time'] = $curl_subscribe_data['data']['end_time'];
				// 将选项数组存入数据库中
				$this->setOptionsSettingsSubscribe_AuthRequired($options_settings_subscribe);
				return true;
			} else {
				return false;
			}
		} else {
			// 如果未请求到数据
			return false;
		}
	}

	/**
	 * 获取主题信息「Logic Safety」
	 * 
	 * @return array|null 主题信息
	 */
	public function getThemeInfo($options_theme) {
		$theme_info_file = './themes/' . $options_theme . '/info.json';
		if (file_exists($theme_info_file)) {
			$theme_info = file_get_contents($theme_info_file);
			$theme_info = json_decode($theme_info, true);
			return $theme_info;
		} else {
			return null;
		}
	}

	/**
	 * 获取主题配置「Logic Safety」
	 * 
	 * @return array|null 主题配置
	 */
	public function getThemeConfig($options_theme) {
		$theme_config_file = './themes/' . $options_theme . '/config.json';
		if (file_exists($theme_config_file)) {
			$theme_config = file_get_contents($theme_config_file);
			$theme_config = json_decode($theme_config, true);
			return $theme_config;
		} else {
			return null;
		}
	}

	/**
	 * HSV 转 RGB「Logic Safety」
	 * 
	 * @param int $h 色调
	 * @param int $s 饱和度
	 * @param int $v 明度
	 * 
	 * @return array RGB 颜色数组
	 */
	public function hsvToRgb($h, $s, $v) {
		$r = $g = $b = 0;

		$i = floor($h * 6);
		$f = $h * 6 - $i;
		$p = $v * (1 - $s);
		$q = $v * (1 - $f * $s);
		$t = $v * (1 - (1 - $f) * $s);
	
		switch ($i % 6) {
			case 0:
				$r = $v;
				$g = $t;
				$b = $p;
				break;
			case 1:
				$r = $q;
				$g = $v;
				$b = $p;
				break;
			case 2:
				$r = $p;
				$g = $v;
				$b = $t;
				break;
			case 3:
				$r = $p;
				$g = $q;
				$b = $v;
				break;
			case 4:
				$r = $t;
				$g = $p;
				$b = $v;
				break;
			case 5:
				$r = $v;
				$g = $p;
				$b = $q;
				break;
		}
	
		return [
			floor($r * 255),
			floor($g * 255),
			floor($b * 255)
		];
	}
}
