(function(){
	console.log('Alpine start loaded');

	function maybeStart(){
		window.__roocmsAlpineStarted = true;
		try { window.__roocmsStartAlpine(); } catch(e) {}
	}

	window.addEventListener('DOMContentLoaded', maybeStart);
	//maybeStart();
})();


