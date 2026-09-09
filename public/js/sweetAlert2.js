export default function sweetAlert() {
    Livewire.on('notification:alert', (event) => {
        const data = event[0];

        Swal.fire({
            icon: data.type,
            title: data.title,
            text: data.text,
			timerProgressBar: true,
			buttonsStyling: true,
        })
    });
};