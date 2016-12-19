import events from './events';
import './events/scroll';
import globals from './globals';
import objectFitImages from 'object-fit-images';
import './modules/preload';

// Defaults
move.defaults = {
	duration: 200,
	ease: 'in-out'
};

// Routing
page({
	dispatch: false
});

// Preloading
const loadPromises = [
	new Promise((resolve) => {
		setTimeout(resolve, 500);
	}),
	new Promise((resolve) => {
		events.on('preloaded', resolve);
	})
];
Promise.all(loadPromises).then(() => {
	document.querySelector('body').classList.add('loaded');
});

// Polyfill object-fit
objectFitImages();
