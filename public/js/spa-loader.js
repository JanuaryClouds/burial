let showTimer;
let progressObserver;
let headerResizeObserver;
let loadingContent;
let previousBusy;
let navigationSequence = 0;

function positionNavigationLoader() {
    if (!loadingContent) return;
    const loader = loadingContent.querySelector('[data-spa-navigation-loader]');
    const contentLeft = Math.max(0, loadingContent.getBoundingClientRect().left);
    loader.style.left = `${contentLeft}px`;
    const header = document.getElementById('main-header-web');
    if (!header) return;
    const { bannerWidth, bannerHeight, bannerFlagpoleX } = header.dataset;
    if (!Number(bannerWidth) || !Number(bannerHeight) || !Number(bannerFlagpoleX)) return;
    const bounds = header.getBoundingClientRect();
    // Match the header's centered, cover-sized background, including its crop.
    const scale = Math.max(bounds.width / Number(bannerWidth), bounds.height / Number(bannerHeight));
    const flagpoleX = bounds.left + bounds.width / 2 + (Number(bannerFlagpoleX) - Number(bannerWidth) / 2) * scale;
    const contentCenter = (contentLeft + document.documentElement.clientWidth) / 2;
    loader.style.setProperty('--spa-loader-offset', `${flagpoleX - contentCenter}px`);
}

function hideNavigationLoader() {
    navigationSequence++;
    clearTimeout(showTimer);
    progressObserver?.disconnect();
    progressObserver = null;
    headerResizeObserver?.disconnect();
    headerResizeObserver = null;
    if (!loadingContent) return;
    const loader = loadingContent.querySelector('[data-spa-navigation-loader]');
    loader.hidden = true;
    loader.style.removeProperty('left');
    loader.style.removeProperty('--spa-loader-offset');
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
            const header = document.getElementById('main-header-web');
            if (header) {
                headerResizeObserver = new ResizeObserver(positionNavigationLoader);
                headerResizeObserver.observe(header);
            }

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
