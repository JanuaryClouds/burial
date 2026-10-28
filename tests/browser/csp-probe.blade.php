<div wire:poll.2s="tick">
    <h1>CSP browser checks</h1>
    <p id="csp-counter">Updates: {{ $count }}</p>
    <p id="csp-polls">Polls: {{ $polls }}</p>
    <button type="button" wire:click="increment" class="btn btn-primary">Increment</button>
    <div x-data="{ revealed: false }">
        <input id="csp-secret" x-bind:type="revealed ? 'text' : 'password'" value="Synthetic fixture value" />
        <button type="button" x-on:click="revealed = !revealed" x-bind:aria-pressed="revealed">Toggle visibility</button>
    </div>
    <select class="form-select" data-control="select2" id="csp-select"><option>One</option><option>Two</option></select>
    <x-image.viewer src="/images/CSWDO.webp" alt="Synthetic image" />
    <a href="/__csp/probe?next=1" wire:navigate>Navigate fixture</a>
    <a href="/__csp/document">Download fixture document</a>
    <div id="csp-status">Waiting for browser checks</div>
</div>
