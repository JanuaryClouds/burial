@livewireScripts(['nonce' => $nonce ?? ''])

@stack('scripts')
<script data-navigate-once nonce="{{ $nonce ?? '' }}" src="{{ asset('js/password-visibility.js') }}" defer></script>
