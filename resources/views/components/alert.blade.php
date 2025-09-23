@if (session('alertSuccess'))
    <script>
        swal("Success", "{{ session('alertSuccess') }}", "success");
    </script>
@elseif (session('alertError'))
    <script>
        swal("Error", "{{ session('alertError') }}", "error");
    </script>
@elseif (session('alertWarning'))
    <script>
        swal("Warning", "{{ session('alertWarning') }}", "warning");
    </script>
@elseif (session('alertInfo'))
    <script>
        swal("Notice", "{{ session('alertInfo') }}", "info");
    </script>
@endif