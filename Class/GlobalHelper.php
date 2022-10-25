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

namespace OpenNav\Class;

use Medoo\Medoo;
use Pdp\Rules;
use Pdp\Domain;

/** OpenNav 全局 Helper 类 */
class GlobalHelper {
	/**
	 * 传入的 Medoo 数据库对象
	 *
	 * @var Medoo
	 */
	private $database;

	/**
	 * 类构造函数
	 *
	 * @param Medoo $database Medoo 数据库对象
	 */
	public function __construct(?Medoo $database) {
		$this->database = $database;
	}

	/**
	 * 获取访客 IP 地址「Logic Safety」
	 *
	 * @return string|false 访客 IP 地址
	 */
	private function getVisitorIP(): string|bool {
		foreach (
			[
				'HTTP_CLIENT_IP',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED',
				'HTTP_X_CLUSTER_CLIENT_IP',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED',
				'REMOTE_ADDR'
			]
			as $key
		) {
			if (array_key_exists($key, $_SERVER)) {
				foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
					return filter_var(
						$ip,
						FILTER_VALIDATE_IP,
						FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
					);
				}
			}
		}
		return false;
	}

	/**
	 * 以 Json 格式返回错误「Logic Safety」
	 *
	 * @param int $code 错误代码
	 * @param string $message 错误提示
	 */
	public function throwError(int $code = 403, string $message = 'error'): void {
		$data = [
			'code' => $code,
			'message' => $message
		];
		header('Content-Type: application/json; charset=utf-8');
		exit(json_encode($data));
	}

	/**
	 * 以 Json 格式返回数据「Logic Safety」
	 *
	 * @param array $data 待返回的数据
	 */
	public function returnSuccess(array $data = []): void {
		if (is_array($data)) {
			if (empty($data['code'])) {
				$data['code'] = 200;
			}
			if (empty($data['message'])) {
				$data['message'] = 'success';
			}
			header('Content-Type: application/json; charset=utf-8');
			exit(json_encode($data));
		} else {
			$this->throwError(500, '内部错误！');
		}
	}

	/**
	 * 使用 PHP Client URL 请求获取数据「Logic Safety」
	 *
	 * @param	string		$url		预请求的 URL 地址
	 * @param	array|null	$post_array	需要 POST 发送的数据
	 * @param	int			$timeout	请求超时时间，以秒为单位
	 *
	 * @return	string|false			获取到的数据，false 代表请求失败
	 */
	public function curlGet(
		string $url,
		?array $post_array = null,
		int $timeout = 10
	): string|bool {
		if (!$this->validateUrl($url)) {
			return false;
		}
		$curl = curl_init($url);
		// 设置 UserAgent
		curl_setopt(
			$curl,
			CURLOPT_USERAGENT,
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36'
		);
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
	public function isLogin(): bool {
		$visitor_ip = $this->getVisitorIP();
		$visitor_infomation = $visitor_ip ? $visitor_ip : $_SERVER['HTTP_USER_AGENT'];
		$local_key = USERNAME . PASSWORD . COOKIE_SECRET_KEY . 'opennav' . $visitor_infomation;
		$local_key_hash = hash('sha256', $local_key);
		// 获取 Session Cookie
		$cookie_session_key = !empty($_COOKIE['opennav_session_key'])
			? $_COOKIE['opennav_session_key']
			: '';
		// 如果已经成功登录
		if ($cookie_session_key === $local_key_hash) {
			// 判断 Session 模式，如为 normal 则延长 Cookie 有效期
			$cookie_session_mode = !empty($_COOKIE['opennav_session_mode'])
				? $_COOKIE['opennav_session_mode']
				: '';
			if ($cookie_session_mode === 'normal') {
				setcookie(
					'opennav_session_mode',
					'normal',
					time() + 60 * 60 * 24 * 30,
					'/',
					null,
					false,
					true
				);
				if (ONLY_SECURE_CONNECTION === true) {
					setcookie(
						'opennav_session_key',
						$local_key_hash,
						time() + 60 * 60 * 24 * 30,
						'/',
						null,
						true,
						true
					);
				} else {
					setcookie(
						'opennav_session_key',
						$local_key_hash,
						time() + 60 * 60 * 24 * 30,
						'/',
						null,
						false,
						true
					);
				}
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
	public function setLogin_AuthRequired(): bool {
		$visitor_ip = $this->getVisitorIP();
		$visitor_infomation = $visitor_ip ? $visitor_ip : $_SERVER['HTTP_USER_AGENT'];
		$local_key = USERNAME . PASSWORD . COOKIE_SECRET_KEY . 'opennav' . $visitor_infomation;
		$local_key_hash = hash('sha256', $local_key);
		setcookie(
			'opennav_session_mode',
			'normal',
			time() + 60 * 60 * 24 * 30,
			'/',
			null,
			false,
			true
		);
		if (ONLY_SECURE_CONNECTION === true) {
			// 仅 HTTPS 设置 Session Cookie
			return setcookie(
				'opennav_session_key',
				$local_key_hash,
				time() + 60 * 60 * 24 * 30,
				'/',
				null,
				true,
				true
			);
		} else {
			// 设置 Session Cookie
			return setcookie(
				'opennav_session_key',
				$local_key_hash,
				time() + 60 * 60 * 24 * 30,
				'/',
				null,
				false,
				true
			);
		}
	}

	/**
	 * 仅时基登录时设置短时登录状态「Auth Required」
	 *
	 * @return bool 设置状态
	 */
	public function setLoginByOnlyTimeBaseValidator_AuthRequired(): bool {
		$visitor_ip = $this->getVisitorIP();
		$visitor_infomation = $visitor_ip ? $visitor_ip : $_SERVER['HTTP_USER_AGENT'];
		$local_key = USERNAME . PASSWORD . COOKIE_SECRET_KEY . 'opennav' . $visitor_infomation;
		$local_key_hash = hash('sha256', $local_key);
		setcookie('opennav_session_mode', 'timebase', time() + 60 * 30, '/', null, false, true);
		if (ONLY_SECURE_CONNECTION === true) {
			// 仅 HTTPS 设置 Session Cookie
			return setcookie(
				'opennav_session_key',
				$local_key_hash,
				time() + 60 * 30,
				'/',
				null,
				true,
				true
			);
		} else {
			// 设置 Session Cookie
			return setcookie(
				'opennav_session_key',
				$local_key_hash,
				time() + 60 * 30,
				'/',
				null,
				false,
				true
			);
		}
	}

	/**
	 * 移除登录状态「Logic Safety」
	 *
	 * @return bool 设置状态
	 */
	public function removeLogin(): bool {
		setcookie('opennav_session_mode', '', time() - 3600, '/', null, false, true);
		// 设置 Session Cookie
		return setcookie('opennav_session_key', '', time() - 3600, '/', null, false, true);
	}

	/**
	 * 获取暗色模式状态「Logic Safety」
	 *
	 * @return bool 暗色模式状态
	 */
	public function isDarkMode(): bool {
		$cookie_theme_layout = isset($_COOKIE['opennav_theme_layout'])
			? $_COOKIE['opennav_theme_layout']
			: '';
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
	public function getRandomKey(int $length = 64, bool $symbol = false): string {
		$charset = $symbol
			? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_[]{}<>~`+=,.;:/?|'
			: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
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
	public function getParentCategories(): array {
		$parent_categories = [];
		if ($this->isLogin()) {
			// 查询一级分类
			$parent_categories = $this->database->select('on_categories', '*', [
				'fid' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
		} else {
			// 查询一级分类
			$parent_categories = $this->database->select('on_categories', '*', [
				'fid' => 0,
				'property' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
		}
		return $parent_categories;
	}

	/**
	 * 获取一级分类「Auth Required」
	 *
	 * @return array 一级分类二维数组
	 */
	public function getParentCategories_AuthRequired(): array {
		$parent_categories = $this->database->select('on_categories', '*', [
			'fid' => 0,
			'ORDER' => ['id' => 'ASC']
		]);
		return $parent_categories;
	}

	/**
	 * 获取一级分类 [id, title]「Auth Required」
	 *
	 * @return array 一级分类二维数组 [id, title]
	 */
	public function getParentCategoriesIdTitle_AuthRequired(): array {
		$parent_categories = $this->database->select(
			'on_categories',
			['id', 'title'],
			[
				'fid' => 0,
				'ORDER' => ['id' => 'ASC']
			]
		);
		return $parent_categories;
	}

	/**
	 * 获取指定一级分类 ID 的二级分类「With Auth」
	 *
	 * @param	int		$parent_category_id	一级分类 ID
	 *
	 * @return	array	二级分类二维数组
	 */
	public function getChildCategoriesByParentCategoryId(int $parent_category_id): array {
		$child_categories = [];
		if ($this->isLogin()) {
			// 查询二级分类
			$child_categories = $this->database->select('on_categories', '*', [
				'fid' => $parent_category_id,
				'ORDER' => ['weight' => 'DESC']
			]);
		} else {
			// 查询二级分类
			$child_categories = $this->database->select('on_categories', '*', [
				'fid' => $parent_category_id,
				'property' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
		}
		return $child_categories;
	}

	/**
	 * 获取分类「With Auth」
	 *
	 * @return array 分类二维数组
	 */
	public function getCategories(): array {
		$categories = [];
		if ($this->isLogin()) {
			// 查询一级分类
			$category_parent_array = $this->database->select('on_categories', '*', [
				'fid' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
			// 遍历一级分类
			foreach ($category_parent_array as $category_parent_value) {
				// 将一级分类追加到 $categories 数组
				array_push($categories, $category_parent_value);
				// 查询二级分类
				$category_child_array = $this->database->select('on_categories', '*', [
					'fid' => $category_parent_value['id'],
					'ORDER' => ['weight' => 'DESC']
				]);
				// 遍历该一级分类下的所有子分类
				foreach ($category_child_array as $category_child_value) {
					// 将所有子分类追加到 $categories 数组
					array_push($categories, $category_child_value);
				}
			}
		} else {
			// 查询一级分类
			$category_parent_array = $this->database->select('on_categories', '*', [
				'fid' => 0,
				'property' => 0,
				'ORDER' => ['weight' => 'DESC']
			]);
			// 遍历一级分类
			foreach ($category_parent_array as $category_parent_value) {
				// 将一级分类追加到 $categories 数组
				array_push($categories, $category_parent_value);
				// 查询二级分类
				$category_child_array = $this->database->select('on_categories', '*', [
					'fid' => $category_parent_value['id'],
					'property' => 0,
					'ORDER' => ['weight' => 'DESC']
				]);
				// 遍历该一级分类下的所有子分类
				foreach ($category_child_array as $category_child_value) {
					// 将所有子分类追加到 $categories 数组
					array_push($categories, $category_child_value);
				}
			}
		}
		return $categories;
	}

	/**
	 * 获取分类 [id, title]「Auth Required」
	 *
	 * @return array 分类二维数组 [id, title]
	 */
	public function getCategoriesIdTitle_AuthRequired(): array {
		$categories = $this->database->select(
			'on_categories',
			['id', 'title'],
			[
				'ORDER' => ['id' => 'ASC']
			]
		);
		return $categories;
	}

	/**
	 * 获取指定分类 ID 的分类「With Auth」
	 *
	 * @param	int		$category_id	分类 ID
	 *
	 * @return	array	分类数组
	 */
	public function getCategoryByCategoryId(int $category_id): array {
		$category_value = [];
		if ($this->isLogin()) {
			$category_value = $this->database->get('on_categories', '*', [
				'id' => $category_id
			]);
		} else {
			$category_value = $this->database->get('on_categories', '*', [
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
	public function getCategoryByCategoryId_AuthRequired(int $category_id): array {
		$category_value = $this->database->get('on_categories', '*', [
			'id' => $category_id
		]);
		return $category_value;
	}

	/**
	 * 获取指定分类 ID 的分类 title「Auth Required」
	 *
	 * @deprecated
	 *
	 * @param	int		$category_id	分类 ID
	 *
	 * @return	string	分类 title
	 */
	public function getCategoryTitleByCategoryId_AuthRequired(int $category_id): string {
		$category_value_title = $this->database->get('on_categories', 'title', [
			'id' => $category_id
		]);
		return $category_value_title;
	}

	/**
	 * 获取指定分类 ID 的分类 title「Auth Required」
	 *
	 * @param	int|array		$categories_id	分类 ID，可为整型或整型数组
	 *
	 * @return	string|array	分类 title，如输入的分类 ID 为数组，则返回值为数组
	 */
	public function getCategoriesTitleByCategoriesId_AuthRequired(
		int|array $categories_id
	): string|array {
		$categories_title = $this->database->select('on_categories', 'title', [
			'id' => $categories_id
		]);
		if (is_array($categories_id)) {
			return $categories_title;
		} else {
			return $categories_title[0];
		}
	}

	/**
	 * 获取指定分类 ID 的分类 [fid, property]「Auth Required」
	 *
	 * @param	int		$category_id	分类 ID
	 *
	 * @return	array	分类数组 [fid, property]
	 */
	public function getCategoryFidPropertyByCategoryId_AuthRequired(int $category_id): array {
		$category_value = $this->database->get(
			'on_categories',
			['fid', 'property'],
			[
				'id' => $category_id
			]
		);
		return $category_value;
	}

	/**
	 * 获取指定分类 ID 的链接「With Auth」
	 *
	 * @param	int		$category_id	分类 ID
	 *
	 * @return	array	链接二维数组
	 */
	public function getLinksByCategoryId(int $category_id): array {
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
	 * 获取链接 url「Auth Required」
	 *
	 * @return array 链接数组 url
	 */
	public function getLinksUrl_AuthRequired(): array {
		return $this->database->select('on_links', 'url');
	}

	/**
	 * 获取指定链接 ID 的链接「With Auth」
	 *
	 * @param	int		$link_id	链接 ID
	 *
	 * @return	array	链接数组 url
	 */
	public function getLinkByLinkId(int $link_id): array {
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
	public function getLinkByLinkId_AuthRequired(int $link_id): array {
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
	public function getCategoriesPagination_AuthRequired(int $pages = 0, int $limit = 0): array {
		$categories = [];
		// 查询分类
		if ($pages > 0 && $limit > 0) {
			// 首行数据偏移量
			$offset = ($pages - 1) * $limit;
			$categories = $this->database->select('on_categories', '*', [
				'ORDER' => ['id' => 'ASC'],
				'LIMIT' => [$offset, $limit]
			]);
		} else {
			$categories = $this->database->select('on_categories', '*', [
				'ORDER' => ['id' => 'ASC']
			]);
		}
		return $categories;
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
	public function getLinksPagination_AuthRequired(int $pages = 0, int $limit = 0): array {
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
	 * 获取分类总数「Auth Required」
	 *
	 * @return int 分类总数
	 */
	public function countCategories_AuthRequired(): int {
		return $this->database->count('on_categories');
	}

	/**
	 * 获取链接总数「Auth Required」
	 *
	 * @return int 链接总数
	 */
	public function countLinks_AuthRequired(): int {
		return $this->database->count('on_links');
	}

	/**
	 * 获取指定分类 ID 的链接总数「Auth Required」
	 *
	 * @param int $category_id 分类 ID
	 *
	 * @return int 链接总数
	 */
	public function countLinksByCategoryId_AuthRequired(int $category_id): int {
		return $this->database->count('on_links', [
			'fid' => $category_id
		]);
	}

	/**
	 * 添加分类「Auth Required」
	 *
	 * @param	array	$category_data	分类数据：[fid, weight, title, font_icon, description, property]
	 * @param	bool	$return_id		是否返回分类 ID
	 *
	 * @return	int|true|string			修改状态，失败时返回 string
	 */
	public function addCategory_AuthRequired(
		array $category_data,
		bool $return_id = false
	): int|bool|string {
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
			$parent_categories = $this->database->select('on_categories', 'id', [
				'fid' => 0
			]);
			// 如果不是一级分类的 id，则数据不合法
			if (!in_array($category_data['fid'], $parent_categories)) {
				return '父级分类必须为一级分类！';
			} else {
				// 否则数据合法，写入数据库
				$category_data['add_time'] = time();
				$this->database->insert('on_categories', $category_data);
				return true;
			}
		} else {
			// 否则数据合法，写入数据库
			$category_data['add_time'] = time();
			$this->database->insert('on_categories', $category_data);
			if ($return_id === true) {
				return intval($this->database->id());
			} else {
				return true;
			}
		}
	}

	/**
	 * 添加链接「Auth Required」
	 *
	 * @param	array	$link_data	链接数据：[fid, weight, title, url, url_standby, description, property]
	 * @param	bool	$return_id	是否返回链接 ID
	 *
	 * @return	int|true|string		添加状态，失败时返回 string
	 */
	public function addLink_AuthRequired(
		array $link_data,
		bool $return_id = false
	): int|bool|string {
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
		// 判断数据合法性
		if (!$this->validateUrl($link_data['url'])) {
			return '链接不合法！';
		}
		if (!empty($link_data['url_standby'])) {
			if (!$this->validateUrl($link_data['url_standby'])) {
				return '外部等待页链接不合法！';
			}
		}

		$category = $this->database->get('on_categories', 'id', [
			'id' => $link_data['fid']
		]);
		if (empty($category)) {
			return '所属分类不存在！';
		}
		// 数据合法，写入数据库
		$link_data['add_time'] = time();
		$this->database->insert('on_links', $link_data);
		if ($return_id === true) {
			return intval($this->database->id());
		} else {
			return true;
		}
	}

	/**
	 * 修改分类「Auth Required」
	 *
	 * @param	int		$category_id	分类 ID
	 * @param	array	$category_data	分类数据：[fid, weight, title, font_icon, description, property]
	 *
	 * @return	true|string				修改状态，失败时返回 string
	 */
	public function updateCategory_AuthRequired(
		int $category_id,
		array $category_data
	): bool|string {
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
			$parent_categories = $this->database->select('on_categories', 'id', [
				'fid' => 0
			]);
			// 如果不是一级分类的 id，则数据不合法
			if (!in_array($category_data['fid'], $parent_categories)) {
				return '父级分类必须为一级分类！';
			} else {
				// 否则数据合法，写入数据库
				$category_data['update_time'] = time();
				$this->database->update('on_categories', $category_data, [
					'id' => $category_id
				]);
				return true;
			}
		} else {
			// 否则数据合法，写入数据库
			$category_data['update_time'] = time();
			$this->database->update('on_categories', $category_data, [
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
	public function updateLink_AuthRequired(int $link_id, array $link_data): bool|string {
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
		// 判断数据合法性
		if (!$this->validateUrl($link_data['url'])) {
			return '链接不合法！';
		}
		if (!empty($link_data['url_standby'])) {
			if (!$this->validateUrl($link_data['url_standby'])) {
				return '外部等待页链接不合法！';
			}
		}

		$category = $this->database->get('on_categories', 'id', [
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
	public function deleteCategory_AuthRequired(int $category_id): bool {
		$category_value = $this->database->get('on_categories', 'fid', [
			'id' => $category_id
		]);
		$child_categories = null;
		if ($category_value['fid'] === 0) {
			$child_categories = $this->database->select('on_categories', 'id', [
				'fid' => $category_id
			]);
		}
		$links = $this->database->select('on_links', 'id', [
			'fid' => $category_id
		]);
		if (empty($child_categories) && empty($links)) {
			$this->database->delete('on_categories', [
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
	public function deleteLink_AuthRequired(int $link_id): void {
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
	public function setLinkValueClick(int $link_id, string $mode = 'add', int $count = 1): bool {
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
			$this->database->update(
				'on_links',
				[
					'click' => $link_value_click
				],
				[
					'id' => $link_id
				]
			);
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
	public function getOptionsTheme(): string {
		$options_theme = $this->database->get('on_options', 'value', [
			'key' => 'theme'
		]);
		return $options_theme;
	}

	/**
	 * 修改主题选项「Auth Required」
	 *
	 * @todo #TODO# 数据校验
	 *
	 * @param string $options_theme 主题
	 */
	public function setOptionsTheme_AuthRequired(string $options_theme): void {
		$options_theme = $this->database->update(
			'on_options',
			[
				'value' => $options_theme
			],
			[
				'key' => 'theme'
			]
		);
	}

	/**
	 * 获取站点设置选项「Logic Safety」
	 *
	 * @return array 站点设置
	 */
	public function getOptionsSettingsSite(): array {
		$options_settings_site = $this->database->get('on_options', 'value', [
			'key' => 'settings_site'
		]);
		$options_settings_site = unserialize($options_settings_site);
		return $options_settings_site;
	}

	/**
	 * 修改站点设置选项「Auth Required」
	 *
	 * @todo #TODO# 数据校验
	 *
	 * @param array $options_settings_site 站点设置
	 */
	public function setOptionsSettingsSite_AuthRequired(array $options_settings_site): void {
		$options_settings_site = serialize($options_settings_site);
		$options_settings_site = $this->database->update(
			'on_options',
			[
				'value' => $options_settings_site
			],
			[
				'key' => 'settings_site'
			]
		);
	}

	/**
	 * 获取过渡页设置选项「Logic Safety」
	 *
	 * @return array 过渡页设置
	 */
	public function getOptionsSettingsTransitionPage(): array {
		$options_settings_transition_page = $this->database->get('on_options', 'value', [
			'key' => 'settings_transition_page'
		]);
		$options_settings_transition_page = unserialize($options_settings_transition_page);
		return $options_settings_transition_page;
	}

	/**
	 * 修改过渡页设置选项「Auth Required」
	 *
	 * @todo #TODO# 数据校验
	 *
	 * @param array $options_settings_transition_page 过渡页设置
	 */
	public function setOptionsSettingsTransitionPage_AuthRequired(
		array $options_settings_transition_page
	): void {
		$options_settings_transition_page = serialize($options_settings_transition_page);
		$options_settings_transition_page = $this->database->update(
			'on_options',
			[
				'value' => $options_settings_transition_page
			],
			[
				'key' => 'settings_transition_page'
			]
		);
	}

	/**
	 * 获取订阅设置选项「Auth Required」
	 *
	 * @return array 订阅设置
	 */
	public function getOptionsSettingsSubscribe_AuthRequired(): array {
		$options_settings_subscribe = $this->database->get('on_options', 'value', [
			'key' => 'settings_subscribe'
		]);
		$options_settings_subscribe = unserialize($options_settings_subscribe);
		return $options_settings_subscribe;
	}

	/**
	 * 修改订阅设置选项「Auth Required」
	 *
	 * @todo #TODO# 数据校验
	 *
	 * @param array $options_settings_subscribe 订阅设置
	 */
	public function setOptionsSettingsSubscribe_AuthRequired(
		array $options_settings_subscribe
	): void {
		$options_settings_subscribe = serialize($options_settings_subscribe);
		$options_settings_subscribe = $this->database->update(
			'on_options',
			[
				'value' => $options_settings_subscribe
			],
			[
				'key' => 'settings_subscribe'
			]
		);
	}

	/**
	 * 获取订阅状态「Logic Safety」
	 *
	 * @return bool 订阅状态
	 */
	public function isSubscribe(): bool {
		// 获取选项数组
		$options_settings_subscribe = $this->getOptionsSettingsSubscribe_AuthRequired();
		// 处理 domain 变量并存入选项数组
		$options_settings_subscribe['domain'] = $this->getPublicSuffixListRegistrableDomain(
			$_SERVER['HTTP_HOST']
		);
		// 请求查询接口返回数据
		$curl_subscribe_data = $this->curlGet(
			API_URL . 'CheckSubscribe.php',
			$options_settings_subscribe
		);
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
	 * @param	string		$options_theme	主题名称
	 *
	 * @return	array|null	主题信息
	 */
	public function getThemeInfo(string $options_theme): ?array {
		$theme_info_file = './themes/' . $options_theme . '/opennav.info.json';
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
	 * @param	string		$options_theme	主题名称
	 *
	 * @return	array|null	主题配置
	 */
	public function getThemeConfig(string $options_theme): ?array {
		$theme_config_file = './themes/' . $options_theme . '/opennav.config.json';
		if (file_exists($theme_config_file)) {
			$theme_config = file_get_contents($theme_config_file);
			$theme_config = json_decode($theme_config, true);
			return $theme_config;
		} else {
			return null;
		}
	}

	/**
	 * 修改全局配置「Auth Required」
	 *
	 * @param string $key 全局变量名
	 * @param string|int|bool $old_value 全局变量原值
	 * @param string|int|bool $value 全局变量将要修改为的值
	 *
	 * @return bool 修改状态
	 */
	public function setGlobalConfig_AuthRequired(
		string $key,
		string|int|bool $old_value,
		string|int|bool $value
	): bool {
		if (!empty($key)) {
			$global_config = file_get_contents('../Data/Config.php');

			if (is_string($old_value) && !empty($old_value)) {
				$str_search = 'define(\'' . $key . '\', \'' . $old_value . '\');';
			} elseif ((is_int($old_value) || is_bool($old_value)) && !empty($old_value)) {
				$str_search = 'define(\'' . $key . '\', ' . $old_value . ');';
			} elseif ($old_value === '') {
				$str_search = 'define(\'' . $key . '\', \'\');';
			} elseif ($old_value === 0) {
				$str_search = 'define(\'' . $key . '\', 0);';
			} elseif ($old_value === false) {
				$str_search = 'define(\'' . $key . '\', false);';
			} else {
				return false;
			}
			if (is_string($value) && !empty($value)) {
				$str_replace = 'define(\'' . $key . '\', \'' . $value . '\');';
			} elseif ((is_int($value) || is_bool($value)) && !empty($value)) {
				$str_replace = 'define(\'' . $key . '\', ' . $value . ');';
			} elseif ($value === '') {
				$str_replace = 'define(\'' . $key . '\', \'\');';
			} elseif ($value === 0) {
				$str_replace = 'define(\'' . $key . '\', 0);';
			} elseif ($value === false) {
				$str_replace = 'define(\'' . $key . '\', false);';
			} else {
				return false;
			}

			$global_config = str_replace($str_search, $str_replace, $global_config);
			if (!file_put_contents('../Data/Config.php', $global_config)) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
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
	public function hsvToRgb(int $h, int $s, int $v): array {
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

		return [floor($r * 255), floor($g * 255), floor($b * 255)];
	}

	/**
	 * 验证电子邮箱合法性「Logic Safety」
	 *
	 * @param	string	$email			电子邮箱
	 * @param	bool	$return_string	是否返回字符串
	 *
	 * @return	string|bool				合法性
	 */
	public function validateEmail(string $email, bool $return_string = false): string|bool {
		$email = filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE);
		if ($email !== false) {
			if ($return_string) {
				return $email;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * 验证密码合法性「Logic Safety」
	 *
	 * @param	string	$password		密码
	 * @param	bool	$return_string	是否返回字符串
	 *
	 * @return	string|bool				合法性
	 */
	public function validatePassword(string $password, bool $return_string = false): string|bool {
		$password_regex = '/^[0-9a-zA-Z!@#$%^&*()-_\[\]\{\}<>~`\+=,.;:\/?|]{6,128}$/';
		if (preg_match($password_regex, $password)) {
			if ($return_string) {
				return $password;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * 验证用户名合法性「Logic Safety」
	 *
	 * @param	string	$username		用户名
	 * @param	bool	$return_string	是否返回字符串
	 *
	 * @return	string|bool				合法性
	 */
	public function validateUsername(string $username, bool $return_string = false): string|bool {
		$username_regex = '/^[0-9a-zA-Z]{3,32}$/';
		if (preg_match($username_regex, $username)) {
			if ($return_string) {
				return $username;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * 验证 URL 合法性「Logic Safety」
	 *
	 * @param	string	$url			URL
	 * @param	bool	$return_string	是否返回字符串
	 *
	 * @return	string|bool				合法性
	 */
	public function validateUrl(string $url, bool $return_string = false): string|bool {
		$url = filter_var($url, FILTER_VALIDATE_URL);
		if ($url !== false) {
			if ($return_string) {
				return $url;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * 验证授权密钥合法性「Logic Safety」
	 *
	 * @param	string	$license_key	授权密钥
	 * @param	bool	$return_string	是否返回字符串
	 *
	 * @return	string|bool				合法性
	 */
	public function validateLicenseKey(
		string $license_key,
		bool $return_string = false
	): string|bool {
		$license_key_regex = '/^ON-[0-9A-Z]{5}-[0-9A-Z]{5}-[0-9A-Z]{5}-[0-9A-Z]{5}$/';
		if (preg_match($license_key_regex, $license_key)) {
			if ($return_string) {
				return $license_key;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * 获取 PublicSuffixList 根域名「Logic Safety」
	 *
	 * @param	string	$url	URL
	 *
	 * @return	string	PublicSuffixList 根域名
	 */
	public function getPublicSuffixListRegistrableDomain(string $url): string {
		$public_suffix_list = Rules::fromPath('../Data/PublicSuffixList.dat');
		$domain = Domain::fromIDNA2008($url);
		$result = $public_suffix_list->resolve($domain);
		return $result->registrableDomain()->toString();
	}
}
