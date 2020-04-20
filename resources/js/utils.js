export function $(selector, scope = document) {
    return scope.querySelector(selector);
}

export function $$(selector, scope = document) {
    return Array.from(scope.querySelectorAll(selector));
}

export function listen(type, selector, callback) {
    document.addEventListener(type, event => {
        const target = event.target.closest(selector);

        if (target) {
            callback(event, target);
        }
    });
}
