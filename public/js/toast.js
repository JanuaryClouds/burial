export default function toast() {
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toastr-bottom-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    Livewire.on('notification:toast', (event) => {
        const data = event[0];

        if (data.type === 'success') {
            toastr.success(data.text, data.title);
        } else if (data.type === 'error') {
            toastr.error(data.text, data.title);
        } else if (data.type === 'warning') {
            toastr.warning(data.text, data.title);
        } else if (data.type === 'info') {
            toastr.info(data.text, data.title);
        }
    });
}