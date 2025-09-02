export default function verification() {
	console.log('Verification component loaded');

	document.addeventlistener("DOMContentLoaded", () => {
		const submitButton = document.getElementById('submitButton');
		const representativeEmail = document.getElementById('representative_email').value;
		const verifyButton = document.getElementById('verifyButton');
		const verifyError = document.getElementById('verifyError');
		const form = document.getElementById('burialForm');

		let verificationCode = null;

		submitButton.addEventListener('click', async () => {
			const email = representativeEmail.value;
			if (!email) {
				alert('Please provide a representative email before submitting the form.');
				return;
			}

			let response = await fetch('/send-verification-code', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				},
				body: JSON.stringify({ email })
			});

			let data = await response.json();
			if (data.success) {
				let modal = new bootstrap.Modal(document.getElementById('verificationModal'));
				modal.show();
			} else {
				alert('Failed to send verification code. Please try again.');
			}
		});

		verifyButton.addEventListener('click', async () => {
			const code = document.querySelector('[x-model="code"]').value;

			let response = await fetch('/verify-code', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
				},
				body: JSON.stringify({ code })
			});

			let data = await response.json();
			if (data.success) {
				form.submit();
			} else {
				verification.style.display = 'block';
				verifyError.textContent = 'Invalid verification code. Please try again.';
			}
		});
	});
}