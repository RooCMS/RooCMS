// Instruct Alpine to defer its start until we explicitly call it later
// This lets us load page modules first (which register Alpine.data components)
window.deferLoadingAlpine = function(init) {
	// Guard against multiple assignments
	if (!window.__roocmsStartAlpine) {
		window.__roocmsStartAlpine = init;
	}
};


