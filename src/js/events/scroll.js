import events from '.';
import getScrollPosition from '../utils/scroll-position';

window.addEventListener('scroll', () => events.emit('scroll', getScrollPosition()));
