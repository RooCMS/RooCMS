function registerHomePage(Alpine){
    Alpine.data('homePage', () => ({
        count: 0,
        increment(){ this.count++; }
    }));
    // Also expose as global factory to support x-data="homePage"
    window.homePage = () => ({
        count: 0,
        increment(){ this.count++; }
    });
}

if (window.Alpine) {
    registerHomePage(window.Alpine);
}

document.addEventListener('alpine:init', () => {
    registerHomePage(window.Alpine);
});


