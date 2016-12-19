export default function getScrollPosition() {
	return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
}
