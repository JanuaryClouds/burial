<script nonce="{{ $nonce ?? '' }}">
    window.sweetalert = function(title, icon, message, confirm = true, cancel = false) {
        Swal.fire({
            title,
            icon,
            text: message,
            showConfirmButton: confirm,
            showCancelButton: cancel,
            timerProgressBar: true,
            buttonsStyling: true,
        });
    };
</script>
@if (session('success'))
    <script nonce="{{ $nonce ?? '' }}">
        sweetalert(
            '',
            'success',
            '{{ session('success') }}',
            true
        )
    </script>
@elseif (session('warning'))
    <script nonce="{{ $nonce ?? '' }}">
        sweetalert(
            '',
            'warning',
            '{{ session('warning') }}',
            true
        )
    </script>
@elseif (session('error'))
    <script nonce="{{ $nonce ?? '' }}">
        sweetalert(
            '',
            'error',
            '{{ session('error') }}',
            true
        )
    </script>
@elseif (session('info'))
    <script nonce="{{ $nonce ?? '' }}">
        sweetalert(
            '',
            'info',
            '{{ session('info') }}',
            true
        )
    </script>
@endif
@if ($errors->any())
    <script nonce="{{ $nonce ?? '' }}">
        sweetalert(
            'Form Error',
            'error',
            'The submitted form has missing fields or has invalid data.',
            true
        )
    </script>
@endif
