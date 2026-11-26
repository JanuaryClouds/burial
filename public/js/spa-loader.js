let showTimer;
let progressObserver;
let loadingContent;
let previousBusy;
let navigationSequence = 0;

function positionNavigationLoader() {
    if (!loadingContent) return;
    const loader = loadingContent.querySelector('[data-spa-navigation-loader]');
    loader.style.left = `${Math.max(0, loadingContent.getBoundingClientRect().left)}px`;
}

function hideNavigationLoader() {
    navigationSequence++;
    clearTimeout(showTimer);
    progressObserver?.disconnect();
    progressObserver = null;
    if (!loadingContent) return;
    const loader = loadingContent.querySelector('[data-spa-navigation-loader]');
    loader.hidden = true;
    loader.style.removeProperty('left');
    loadingContent.classList.remove('spa-content-loading');
    if (previousBusy === null) loadingContent.removeAttribute('aria-busy');
    else loadingContent.setAttribute('aria-busy', previousBusy);
    loadingContent = null;
}

document.addEventListener('livewire:navigate', event => {
    hideNavigationLoader();
    const sequence = navigationSequence;
    queueMicrotask(() => {
        if (event.defaultPrevented || sequence !== navigationSequence) return;
        showTimer = setTimeout(() => {
            if (sequence !== navigationSequence) return;
            const loader = document.querySelector('[data-spa-navigation-loader]');
            if (!loader) return;
            loadingContent = loader.parentElement;
            previousBusy = loadingContent.getAttribute('aria-busy');
            loadingContent.classList.add('spa-content-loading');
            loadingContent.setAttribute('aria-busy', 'true');
            positionNavigationLoader();
            loader.hidden = false;

            // Restore the old content on a failed request when Livewire removes its progress bar.
            let progressSeen = Boolean(document.getElementById('nprogress'));
            progressObserver = new MutationObserver(() => {
                const progress = document.getElementById('nprogress');
                if (progress) progressSeen = true;
                else if (progressSeen) hideNavigationLoader();
            });
            progressObserver.observe(document.documentElement, { childList: true, subtree: true });
        }, 180);
    });
});

// Clean up before Livewire captures this page for browser Back/Forward.
document.addEventListener('livewire:navigating', hideNavigationLoader);
document.addEventListener('livewire:navigated', hideNavigationLoader);
window.addEventListener('pageshow', hideNavigationLoader);
window.addEventListener('resize', positionNavigationLoader);
