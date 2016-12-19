import isNode from './is-node';

export default function getParent(elm, selector) {
	let cur = elm.parentNode;
	if (isNode(selector)) {
		while (cur && cur !== selector) {
			cur = cur.parentNode;
		}
	} else {
		const alls = document.querySelectorAll(selector);
		while (cur && [...alls].indexOf(cur) === -1) {
			cur = cur.parentNode;
		}
	}
	return cur;
}
