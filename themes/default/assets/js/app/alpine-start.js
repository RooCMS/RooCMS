(function(){
	console.log('Alpine start loaded');

	function startAlpine(){
		if (window.Alpine) {
			console.log('Starting Alpine.js CSP version...');
			window.__roocmsAlpineStarted = true;
			window.Alpine.start();
		} else {
			console.log('Alpine.js not loaded yet, retrying...');
			setTimeout(startAlpine, 10);
		}
	}

	// Try to start immediately, or wait for DOM and Alpine
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', startAlpine);
	} else {
		startAlpine();
	}
})();


