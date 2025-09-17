(function(){
	function maybeStart(){
		if (!window.__roocmsStartAlpine) return;
		if (window.__roocmsAlpineStarted) return;
		if (!window.__roocmsPageScriptsReady) return;
		window.__roocmsAlpineStarted = true;
		try { window.__roocmsStartAlpine(); } catch(e) {}
	}
	window.addEventListener('roocms:pages-ready', maybeStart);
	window.addEventListener('DOMContentLoaded', maybeStart);
	maybeStart();
})();


