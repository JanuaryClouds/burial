<!-- TODO: Either use Toastr or use bootstrap's toast notification -->
@if (session()->has('success'))
    <script>
        iziToast.success({
            title: 'Success',
            message: '{{ session('success') }}',
            position: 'bottomRight',
            icon: 'fas fa-check-circle',
            backgroundColor: '#ffc83dff', 
        })
        </script>
@elseif (session()->has('error'))
<script>
    iziToast.error({
            title: 'Error',
            message: '{{ session('error') }}',
            position: 'bottomRight',
            messageColor: '#ffffffff',
            icon: 'fas fa-times-circle',
            backgroundColor: '#dc3545ff', 
        })
    </script>
@elseif (session()->has('info'))
    <script>
        iziToast.info({
            title: 'Info',
            message: '{{ session('info') }}',
            messageColor: '#000000',
            position: 'bottomRight',
            icon: 'fas fa-info-circle',
            backgroundColor: '#ffffffff',
        })
    </script>
@endif