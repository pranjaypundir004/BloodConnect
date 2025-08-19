(function () {
    let supportsPassive = false;
    try {
        let opts = Object.defineProperty({}, 'passive', {
            get: function () { supportsPassive = true; }
        });
        window.addEventListener('test', null, opts);
    } catch (e) {}

    window.addEventListener("wheel", function (event) {
        if (event.ctrlKey) { // Prevent zooming, not scrolling
            event.preventDefault();
        }
    }, supportsPassive ? { passive: false } : false);

    window.addEventListener("touchmove", function (event) {
        if (event.scale !== 1) { // Prevent pinch zooming
            event.preventDefault();
        }
    }, supportsPassive ? { passive: false } : false);
})();
