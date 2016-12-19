const container = document.querySelector('[data-jsglobals]');
const globals = JSON.parse(container.dataset.jsglobals) || {};

export default globals;
