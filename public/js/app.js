/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

import './page-loader.js';
import './spa-loader.js';
import './record-export.js';
import './image-viewer.js';
import { initNavigationWidgets, disposeNavigationWidgets } from './navigation-widgets.js';
import initStatusSubmitLoader from './status-submit-loader.js';

import updateDistrict from './districts.js';
import initSelect2 from './select2.js';
import randomizeMulticolorBorder from './multicolorBorder.js';
import { checkCharts, refreshChart, disposeCharts } from './chart.js';
import theme from './theme.js';
import initDashboard from './dashboard.js';
import autoMarginColumns from './autoMarginColumns.js';
import sweetAlert from './sweetAlert2.js';
import initSuperadminAccess from './superadmin-access.js';
import toast from './toast.js';
import { selectDataTables, refreshDataTable } from './datatable.js';

theme();

// Initialize select2s as soon as this module runs. Module scripts execute after
// the DOM is parsed but BEFORE the DOMContentLoaded event, so this beats
// Metronic's own createSelect2 (scripts.bundle.js), which would otherwise
// initialize our selects first WITHOUT the tags option and mark them
// data-kt-initialized, making initSelect2 skip them.
initSelect2();

function initializePage() {
    // Initialize tagged selects before Metronic applies its generic defaults.
    initSelect2();
    initNavigationWidgets();
    checkCharts();
    randomizeMulticolorBorder();
    theme();
    selectDataTables();
    autoMarginColumns();
}

let livewireInitialized = false;

// Expose for vanilla JS that adds dynamic selects (e.g. the beneficiary family
// composition rows) so newly inserted dropdowns can be initialized too.
window.initSelect2 = initSelect2;

function initializeLivewire() {
    if (livewireInitialized) return;
    livewireInitialized = true;
    const pendingMessages = new Map();
    Livewire.interceptMessage(({ message, cancel, onSuccess, onFinish }) => {
        pendingMessages.set(message, cancel);
        onSuccess(() => pendingMessages.delete(message));
        onFinish(() => pendingMessages.delete(message));
    });
    document.addEventListener('livewire:navigating', () => {
        pendingMessages.forEach(cancel => cancel());
        pendingMessages.clear();
    });
    initStatusSubmitLoader();
    initDashboard();
    autoMarginColumns();
    refreshChart();
    sweetAlert();
    initSuperadminAccess();
    toast();
    selectDataTables();
    refreshDataTable();

    Livewire.hook('morph.updated', ({ el }) => {
        requestAnimationFrame(() => {
            if (!el.isConnected) return;
            initSelect2(el);
            randomizeMulticolorBorder();
        });
    });

    Livewire.hook('morph.added', ({ el }) => {
        requestAnimationFrame(() => {
            if (!el.isConnected) return;
            initSelect2(el);
            autoMarginColumns();
        });
    });

    // `morphed` fires after every element in a component has been morphed,
    // including lazy/deferred children whose HTML is loaded separately. It is
    // the most reliable place to initialize select2 for freshly added selects.
    Livewire.hook('morphed', ({ el }) => {
        requestAnimationFrame(() => {
            if (!el.isConnected) return;
            checkCharts();
            selectDataTables();
            initSelect2(el);
            autoMarginColumns();
        });
    });

    Livewire.hook('element.init', ({ el }) => {
        requestAnimationFrame(() => {
            if (!el.isConnected) return;
            initSelect2(el);
            autoMarginColumns();
        });
    });
}

document.addEventListener('livewire:init', initializeLivewire, { once: true });
if (window.Livewire) initializeLivewire();

document.addEventListener('livewire:navigating', () => {
    disposeCharts();
    disposeNavigationWidgets();
});
document.addEventListener('livewire:navigated', () => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePage, { once: true });
    } else {
        initializePage();
    }
});

$(document).ajaxError(function(event, xhr) {
    if (xhr.status === 403) {
        window.location.reload();
    }
});
