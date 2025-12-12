<script>
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
    <script>
        sweetalert(
            '',
            'success',
            '{{ session('success') }}',
            true
        )
    </script>
@elseif (session('warning'))
    <script>
        sweetalert(
            '',
            'warning',
            '{{ session('warning') }}',
            true
        )
    </script>
@elseif (session('error'))
    <script>
        sweetalert(
            '',
            'error',
            '{{ session('error') }}',
            true
        )
    </script>
@elseif (session('info'))
    <script>
        sweetalert(
            '',
            'info',
            '{{ session('info') }}',
            true
        )
    </script>
@endif
