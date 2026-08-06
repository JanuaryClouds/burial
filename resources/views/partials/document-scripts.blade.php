<script nonce="{{ $nonce ?? '' }}"
	src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
	integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
	crossorigin="anonymous"
	referrerpolicy="no-referrer"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
	integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y"
	crossorigin="anonymous"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"
	integrity="sha256-SERKgtTty1vsDxll+qzd4Y2cF9swY9BCq62i9wXJ9Uo="
	crossorigin="anonymous"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"
	integrity="sha512-XMVd28F1oH/O71fzwBnV7HucLxVwtxf26XV8P4wPk26EDxuGZ91N8bsOttmnomcCD3CS5ZMRL50H0GgOHvegtg=="
	crossorigin="anonymous"
	referrerpolicy="no-referrer"></script>
<script nonce="{{ $nonce ?? '' }}"
	src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/pdfmake.min.js"
	integrity="sha512-axXaF5grZBaYl7qiM6OMHgsgVXdSLxqq0w7F4CQxuFyrcPmn0JfnqsOtYHUun80g6mRRdvJDrTCyL8LQqBOt/Q=="
	crossorigin="anonymous"
	referrerpolicy="no-referrer"></script>

<script type="module"
	nonce="{{ $nonce ?? '' }}"
	src="{{ asset('js/app.js') }}"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="{{ asset('metronic/js/scripts.bundle.js') }}"></script>
<script nonce="{{ $nonce ?? '' }}"
	src="{{ asset('metronic/plugins/global/plugins.bundle.js') }}"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="{{ asset('metronic/plugins/custom/datatables/datatables.bundle.js') }}"></script>

@livewireScripts(['nonce' => $nonce ?? ''])
