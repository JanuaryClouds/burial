/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

import updateDistrict from './districts.js';
import initSelect2 from './select2.js';
import randomizeMulticolorBorder from './multicolorBorder.js';
import checkAndRenderCharts from './chart.js';

// Initialize select2s as soon as this module runs. Module scripts execute after
// the DOM is parsed but BEFORE the DOMContentLoaded event, so this beats
// Metronic's own createSelect2 (scripts.bundle.js), which would otherwise
// initialize our selects first WITHOUT the tags option and mark them
// data-kt-initialized, making initSelect2 skip them.
initSelect2();

document.addEventListener('DOMContentLoaded', () => {
    checkAndRenderCharts();
    randomizeMulticolorBorder();
    initSelect2();
    
    $('#barangay_id').on('change', function() {
        let text = $(this).find('option:selected').text();
        updateDistrict(text.trim());
    });

    $('#client_uuid_select').on('change', function(event) {
        const uuid = $(this).val();
        Livewire.dispatch('client-selected', uuid);
    });
    $('#beneficiary_uuid_select').on('change', function(event) {
        const uuid = $(this).val();
        Livewire.dispatch('beneficiary-selected', uuid);
    });
});

// Expose for vanilla JS that adds dynamic selects (e.g. the beneficiary family
// composition rows) so newly inserted dropdowns can be initialized too.
window.initSelect2 = initSelect2;

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => {
        requestAnimationFrame(() => {
            initSelect2(el);
            randomizeMulticolorBorder();
        });
    });

    Livewire.hook('morph.added', ({ el }) => {
        requestAnimationFrame(() => {
            initSelect2(el);
        });
    });

    // `morphed` fires after every element in a component has been morphed,
    // including lazy/deferred children whose HTML is loaded separately. It is
    // the most reliable place to initialize select2 for freshly added selects.
    Livewire.hook('morphed', ({ el }) => {
        requestAnimationFrame(() => {
            initSelect2(el);
        });
    });

    Livewire.hook('element.init', ({ el }) => {
        requestAnimationFrame(() => {
            initSelect2(el);
        });
    });
});

document.addEventListener('livewire:navigated', () => {
    initSelect2();
});

$(document).ajaxError(function(event, xhr) {
    if (xhr.status === 403) {
        window.location.reload();
    }
});
