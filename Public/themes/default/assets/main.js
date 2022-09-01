// 调用 holmes.js 进行搜索
$('#top_search_bar').blur(function(data, status) {
	const keywords = $('#top_search_bar').val();
	if (keywords === '') {
		$('.cat-title').removeClass('mdui-hidden');
	}
});
const h = holmes({
	input: '#top_search_bar',
	find: '.link-space',
	// placeholder: '',
	mark: false,
	hiddenAttr: true,
	// 找到了就添加 visible 类，没找到则添加 mdui-hidden
	class: {
		visible: 'visible',
		hidden: 'mdui-hidden'
	},
	onHidden(el) {},
	onFound(el) {
		$('.cat-title').addClass('mdui-hidden');
	},
	onInput(el) {
		$('.cat-title').addClass('mdui-hidden');
	},
	onVisible(el) {
		$('.cat-title').addClass('mdui-hidden');
	},
	onEmpty(el) {}
});

// 绑定输入焦点到搜索框
function focusSearchBar() {
	$('#top_search_bar').focus();
}

// 返回顶部
function goTop() {
	$('html, body').animate({
		scrollTop: '0px'
	}, 600);
}

// 获取 Cookie
function getCookie(cookieName) {
	const name = cookieName + '=';
	const ca = document.cookie.split(';');
	for (let i = 0; i < ca.length; i++) {
		const c = ca[i].trim();
		if (c.indexOf(name) === 0) {
			return c.substring(name.length, c.length);
		}
	}
	return '';
}

// 切换 MDUI 主题
function changeTheme() {
	let date = new Date();
	date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
	const expires = 'expires=' + date.toGMTString();
	let theme_layout = 'light';

	if ((getCookie('opennav_theme_layout') === '') || (getCookie('opennav_theme_layout') === 'light')) {
		theme_layout = 'dark';
	} else {
		theme_layout = 'light';
	}
	document.cookie = 'opennav_theme_layout=' + theme_layout + '; ' + expires + ' path=/';
	window.location.href = './';
}

// 顶部搜索栏触发搜索
function topSearchBarSearch() {
	const event = window.event || arguments.callee.caller.arguments[0];
	if (event.key == 'Enter' && event.shiftKey == true) {
		const searchBarSearchKey = document.getElementById('top_search_bar').value;
		window.open('https://www.bing.com/search?q=' + searchBarSearchKey, '_blank');
	} else if (event.key == 'Enter') {
		const searchBarSearchKey = document.getElementById('top_search_bar').value;
		window.open('https://www.bing.com/search?q=' + searchBarSearchKey, '_self');
	}
}
