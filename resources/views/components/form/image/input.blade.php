@props([
    'name',
    'id',
    'label' => null,
    'required' => false,
    'helpText' => null,
    'field' => null,
])

@php
	$errorname = $name;
	if (str_contains($name, 'images[')) {
	    $errorname = str_replace('[', '.', str_replace(']', '', $name));
	}

	// The name the file will carry in the form request (e.g. images[death_certificate]).
	$fieldName = $field ?? $name;
@endphp

<div class="dropzone"
	id="{{ $name }}">
	<!--begin::Message-->
	<div class="dz-message needsclick">
		<i class="ki-duotone ki-file-up fs-3x text-primary"><span class="path1"></span><span class="path2"></span></i>

		<!--begin::Info-->
		<div class="ms-4">
			<h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop or upload {{ $label }} here.</h3>
			@isset($helpText)
				<span class="fs-7 fw-semibold text-gray-500">{{ $helpText }}</span>
			@endisset
		</div>
		<!--end::Info-->
	</div>
</div>

<script nonce="{{ $nonce ?? '' }}">
	$(document).ready(function() {
		window.__burialDropzones = window.__burialDropzones || [];

		var dz = window.dz_{{ Str::slug($name, '_') }} = new Dropzone("#{{ $name }}", {
			url: "{{ route('application.store') }}",
			method: "POST",
			paramName: "{{ $fieldName }}",
			maxFiles: 1,
			maxFilesize: 15,
			acceptedFiles: ".png, .jpg, .jpeg",
			addRemoveLinks: true,
			// Do not fire Dropzone's own AJAX upload — files are submitted with the form instead.
			autoProcessQueue: false,
			init: function() {
				var instance = this;
				// The bundled Dropzone keeps rejected files in the queue (marked as
				// error) instead of removing them, and its default maxfilesexceeded
				// handler is a no-op. Remove excess files so only one is ever kept.
				instance.on("maxfilesexceeded", function(file) {
					instance.removeFile(file);
				});
			},
		});

		// The form this dropzone belongs to (the dropzone is rendered inside it).
		var dzForm = dz.element.closest('form');

		window.__burialDropzones.push({
			instance: dz,
			field: "{{ $fieldName }}",
			form: dzForm,
		});

		// Bind the form integration handler only once per page.
		if (!window.__burialDropzoneSubmitBound) {
			window.__burialDropzoneSubmitBound = true;

			$(document).on('submit', 'form', function() {
				var form = this;

				(window.__burialDropzones || []).forEach(function(entry) {
					// Only attach files when this dropzone belongs to the submitted form.
					if (!entry.form || entry.form !== form) {
						return;
					}

					var files = entry.instance.getAcceptedFiles();
					if (!files.length) {
						return;
					}

					// Reuse a file input that already exists inside the form
					// (e.g. Dropzone's own hidden input), otherwise append a hidden one.
					var inputs = Array.prototype.filter.call(form.elements, function(el) {
						return el.tagName === 'INPUT' && el.type === 'file' && el.name === entry.field;
					});
					var input = inputs.length ? inputs[0] : null;

					if (!window.DataTransfer) {
						console.warn('[burial] DataTransfer is not supported — document upload will be skipped.');
						return;
					}

					var dt = new DataTransfer();
					dt.items.add(files[0]);

					if (input) {
						input.disabled = false;
						input.files = dt.files;
					} else {
						input = document.createElement('input');
						input.type = 'file';
						input.name = entry.field;
						input.style.display = 'none';
						input.files = dt.files;
						form.appendChild(input);
					}
				});
			});
		}
	});
</script>
