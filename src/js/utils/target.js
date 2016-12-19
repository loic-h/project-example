import isNode from './is-node';
import getParent from './get-parent';

export default function target(elm, selector) {
	if (isNode(selector)) {
		if(elm === selector) {
			return elm;
		}
	} else if ( elm.matches && elm.matches(selector)) {
		return elm;
	}
	const parent = getParent(elm, selector);
	if (parent) {
		return parent;
	}
}
