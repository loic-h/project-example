import events from '../events';
import createjs from 'preload-js';

const manifestNode = document.querySelector('[data-manifest]');
const body = document.querySelector('body');
const loader = document.querySelector('.loader');
let queue;

function init() {
	const manifest = JSON.parse(manifestNode.dataset.manifest);
	queue = new createjs.LoadQueue(false);
	queue.on('complete', onComplete, this);
	queue.on('progress', onProgress, this);
	queue.on('fileload', onFileLoad, this);
	[...document.querySelectorAll('[data-preload]')].forEach(el => {
		if (el.src) {
			queue.loadFile(el);
		} else if (el.style.backgroundImage) {
			const src = el.dataset.preload;
			queue.loadFile(src);
		}
	});
	queue.loadManifest(manifest);
}

function onComplete() {
	loader.classList.remove('show');
	events.emit('preloaded');
}

function onProgress(event) {
	loader.classList.add('show');
	const width = event.loaded * 100 / event.total;
	loader.style.width = `${width}%`;
}

function onFileLoad(event) {
	let item;
	if (event.item && event.item.dataset && typeof event.item.dataset.preload !== 'undefined') {
		item = event.item;
	} else {
		const el = document.querySelector(`[data-preload='${event.item.src}']`);
		if (el) {
			item = el;
		}
	}
	if (item) {
		item.dataset.preload = 'loaded';
	}
}

if (manifestNode) {
	init();
}
