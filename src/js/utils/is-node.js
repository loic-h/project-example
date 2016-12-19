export default function isNode(el) {
	if(el) {
		return !!el.nodeType;
	}
}
