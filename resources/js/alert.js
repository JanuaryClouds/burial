window.sweetalert = function (title, icon, message, confirm = true, cancel = false) {
    Swal.fire({
        title,
        icon,
        text: message,
        showConfirmButton: confirm,
        showCancelButton: cancel,
        timerProgressBar: true,
    });
};
