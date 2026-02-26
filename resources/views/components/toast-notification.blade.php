@if (session()->has('success'))
    <script nonce="{{ $nonce ?? '' }}">
        iziToast.success({
            title: 'Success',
            message: @json(session('success')),
            position: 'bottomRight',
            icon: 'fas fa-check-circle',
            backgroundColor: '#ffc83dff',
        })
    </script>
@elseif (session()->has('error'))
    <script nonce="{{ $nonce ?? '' }}">
        iziToast.error({
            title: 'Error',
            message: @json(session('error')),
            position: 'bottomRight',
            messageColor: '#ffffffff',
            icon: 'fas fa-times-circle',
            backgroundColor: '#dc3545ff',
        })
    </script>
@elseif (session()->has('info'))
    <script nonce="{{ $nonce ?? '' }}">
        iziToast.info({
            title: 'Info',
            message: @json(session('info')),
            messageColor: '#000000ff',
            position: 'bottomRight',
            icon: 'fas fa-info-circle',
            backgroundColor: '#ffffffff',
        })
    </script>
@endif
