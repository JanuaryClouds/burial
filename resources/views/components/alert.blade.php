@if (session('alert'))
    <script>
        salert(
            '{{ session('alert')['title'] ?? 'Notice' }}',
            '{{ session('alert')['icon'] ?? 'info' }}',
            '{{ session('alert')['message'] ?? '' }}',
            '{{ session('alert')['confirm'] ?? true }}',
            '{{ session('alert')['cancel'] ?? false }}'
        )
    </script>
@endif

<script>
    window.swalert = function(title, icon, message, confirm, cancel, ) {
        Swal.fire({
            title: title,
            icon: icon,
            text: message,
            showConfirmButton: confirm ?? true,
            showCancelButton: cancel ?? false,
            timerProgressBar: true,
        })
    }
</script>
