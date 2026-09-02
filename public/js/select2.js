export default function initSelect2(root = document) {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        return;
    }

    root.querySelectorAll('[data-control="select2"], [data-kt-select2="true"]').forEach((element) => {
        if (element.getAttribute('data-kt-initialized') === '1') {
            return;
        }

        // If Livewire morphed this element in place (without wire:ignore), the
        // previous select2 instance and its container may still be attached.
        // Tear those down first so we never end up with a stale instance or
        // duplicate dropdown container.
        if ($(element).data('select2')) {
            $(element).select2('destroy');
        }

        let sibling = element.nextElementSibling;
        while (sibling && sibling.classList.contains('select2-container')) {
            const next = sibling.nextElementSibling;
            sibling.remove();
            sibling = next;
        }

        const options = {
            dir: document.body.getAttribute('direction'),
        };

        if (element.getAttribute('data-hide-search') === 'true') {
            options.minimumResultsForSearch = Infinity;
        }

        if (element.classList.contains('select-dynamic')) {
            options.tags = true;
        }

        $(element).select2(options);

        // Handle Select2's KTMenu parent case
        if (element.hasAttribute('data-dropdown-parent') && element.hasAttribute('multiple')) {
            const parentEl = document.querySelector(element.getAttribute('data-dropdown-parent'));

            if (parentEl && parentEl.hasAttribute('data-kt-menu') && typeof KTMenu !== 'undefined') {
                const menu = new KTMenu(parentEl);

                if (menu) {
                    $(element).on('select2:unselect', function () {
                        element.setAttribute('data-multiple-unselect', '1');
                    });

                    menu.on('kt.menu.dropdown.hide', function (item) {
                        if (element.getAttribute('data-multiple-unselect') === '1') {
                            element.removeAttribute('data-multiple-unselect');
                            return false;
                        }
                    });
                }
            }
        }

        element.setAttribute('data-kt-initialized', '1');

        $(element)
            .off('change.livewire')
            .on('change.livewire', function () {
                const value = $(this).val();

                const livewireId = element.id.replace('_display', '');
                const input = document.getElementById(livewireId);

                if (input) {
                    input.value = value;
                    input.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            });

        // const select = $wire.$el.querySelector('#{{ $id ?? $name }}');

		// if (select) {
		// 	$(select).on('change', function() {
		// 		const value = $(this).val();
		// 		console.log(select);
		// 		console.log('{{ $name }}', $(select).val(), value);

		// 		$wire.set('{{ $name }}', value, false);
		// 		$('#debug-selected-{{ $name }}').text('Selected: ' + value);
		// 	});
		// }
    });
}
