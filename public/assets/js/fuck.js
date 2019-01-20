var browserHTML = '<div style="padding: 15px"><h2>Chrome</h2><p><a href="https://www.google.cn/chrome">前往 Google Chrome 官网下载 Chrome 浏览器（全平台）</a></p><p><a href="https://lab.skk.moe/chrome">Chrome 离线包下载（仅限 Windows）</a></p><p><a href="https://www.wandoujia.com/apps/com.android.chrome">前往豌豆荚下载 Chrome 安卓版</a></p><p><a href="https://play.google.com/store/apps/details?id=com.android.chrome">前往 Google Play 下载 Chrome 安卓版</a></p><p><a href="https://itunes.apple.com/us/app/chrome/id535886823">前往 App Store 下载 Chrome iOS 版</a></p><h2>Firefox</h2><p><a href="https://www.mozilla.org/zh-CN/firefox/">前往 Mozilla 官网下载 Firefox 浏览器（全平台）</a></p><p><a href="https://www.firefox.com.cn/">前往火狐中文网下载 Firefox 浏览器（全平台）</a></p><p><a href="https://www.wandoujia.com/apps/org.mozilla.firefox">前往豌豆荚下载 Firefox 安卓版</a></p><p><a href="https://www.coolapk.com/apk/org.mozilla.firefox">前往酷安下载 Firefox 安卓版</a></p><p><a href="https://play.google.com/store/apps/details?id=org.mozilla.firefox">前往 Google Play 下载 Firefox 安卓版</a></p></div>';

var mqqHTML = '<div style="padding: 15px"><h2>请从菜单中选择「从浏览器中打开」</h2></div>';

var bodyEl = document.getElementsByTagName('body')[0];

if (navigator.userAgent.toLowerCase().indexOf('miuibrowser') !== -1) {
    alert('MIUI 浏览器屏蔽了本站部分内容的访问\n为了您能更好地浏览本站，我们要求您使用 Chrome 或 Firefox 浏览器。');
    bodyEl.innerHTML = browserHTML;
} else if (navigator.userAgent.toLowerCase().indexOf('ucbrowser') !== -1) {
    alert('UC 浏览器使用极旧的内核，而本网站使用了一些新的特性。\n为了您能更好地浏览本站，我们要求您使用 Chrome 或 Firefox 浏览器。');
    bodyEl.innerHTML = browserHTML;
} else if (navigator.userAgent.toLowerCase().indexOf('tbs') !== -1) {
    alert('腾讯浏览器内核存在严重缺陷。\n为了您能更好地浏览本站，我们要求您使用 Chrome 或 Firefox 浏览器。');
    bodyEl.innerHTML = browserHTML;
} else if (navigator.userAgent.toLowerCase().indexOf('mqq') !== -1) {
    alert('手机 QQ 浏览器内核存在严重缺陷。\n为了您能更好地浏览本站，我们要求您使用浏览器访问本站。');
    bodyEl.innerHTML = mqqHTML;
} else if (navigator.userAgent.toLowerCase().indexOf('qq') !== -1) {
    alert('QQ 浏览器内核存在严重缺陷。\n为了您能更好地浏览本站，我们要求您使用 Chrome 或 Firefox 浏览器。');
    bodyEl.innerHTML = browserHTML;
} else if ((navigator.userAgent.toLowerCase().indexOf('micromessenger') !== -1) || (navigator.userAgent.toLowerCase().indexOf('wechat') !== -1)) {
    alert('为了您的人身安全，我们禁止您使用微信访问本站，我们要求您通过 Chrome 或 Firefox 浏览器访问本站。');
    bodyEl.innerHTML = mqqHTML;
}