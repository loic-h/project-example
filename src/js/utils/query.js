import request from 'superagent';
import move from 'move-js';

const body = document.getElementsByTagName('body')[0];

const token = getToken();

const defaults = {
	ajax: true,
	target: null,
	data: null,
	action: null,
	end: null
};

function query(opts) {
	const options = Object.assign({}, defaults, opts);
	const tokenKey = Object.keys(token)[0];
	options.data.append(tokenKey, token[tokenKey]);
	const req = request
		.post(options.action)
		.send(options.data);

	if (options.ajax) {
		req.set('X-Requested-With', 'XMLHttpRequest');
	}
	req.end((err, res) => {
		if (err) {
			return;
		}
		onEnd(options.target, res, options.end);
	});
}

function onEnd(container, res, cb) {
	if (container && res.type === 'text/html') {
		display(container, res.text, () => callCb(cb, res));
	} else {
		callCb(cb, res);
	}
}

function callCb(cb, args) {
	if (cb) {
		cb(args);
	}
}

function display(container, text, cb) {
	move(container)
		.set('opacity', 0)
		.then(() => container.innerHTML = text)
		.then()
			.set('opacity', 1)
			.pop()
		.end(() => callCb(cb));
}

function getToken(container = document) {
	const node = container.querySelector('div[data-csrf]');
	return JSON.parse(node.dataset.csrf);
}

export {query, display};
