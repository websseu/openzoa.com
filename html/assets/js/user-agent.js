document.addEventListener('DOMContentLoaded', function () {
    const ua = navigator.userAgent.toLowerCase();
    const htmlTag = document.documentElement; // <html> 태그

    if (/iphone|ipad|ipod/i.test(ua)) {
        htmlTag.classList.add('is-ios');
    } else if (/android/i.test(ua)) {
        htmlTag.classList.add('is-android');
    } else {
        htmlTag.classList.add('is-pc');
    }
});