<form wire:submit="submit" x-data="staffLogin" @login-failed="failed($event.detail)">
	@csrf
    <div class="text-center" role="alert" aria-live="polite">
        <template x-if="remaining > 0"><p class="text-danger">Too many failed attempts. Try again in <span x-text="remaining"></span> seconds, or <a href="{{ route('password.request') }}">reset your password</a>.</p></template>
        <div x-show="remaining === 0">
            @error('credentials') <p class="text-danger">{{ $message }}</p> @enderror
        </div>
        @error('email') <p class="text-danger">{{ $message }}</p> @enderror
        @error('password') <p class="text-danger">{{ $message }}</p> @enderror
        <p x-show="recovery && remaining === 0" x-cloak>Having trouble signing in? You can <a href="{{ route('password.request') }}">reset your password</a>.</p>
    </div>
	<div class="mb-4">
		<label for="email"
			class="form-label">Email</label>
		<input type="email"
			id="email"
			name="email" wire:model="email" autocomplete="username" @input="resetFeedback()"
			placeholder="Enter your email"
			class="form-control"
			required>
	</div>
	<div class="mb-6">
		<label for="password"
			class="form-label">Password</label>
		<div class="position-relative">
			<input type="password"
				id="password"
				name="password" wire:model="password" autocomplete="current-password" x-bind:type="visible ? 'text' : 'password'"
				placeholder="Enter your password"
				class="form-control pe-10"
				required>
			<button type="button"
				id="togglePassword" @click="visible = !visible" aria-label="Show or hide password"
				class="btn btn-sm position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent">
				<i class="fa-solid" x-bind:class="visible ? 'fa-eye-slash' : 'fa-eye'"></i>
			</button>
		</div>
	</div>
	<div class="mt-4 d-flex">
		<button type="submit" wire:loading.attr="disabled" x-bind:disabled="remaining > 0"
			class="btn btn-primary w-100"><span wire:loading.remove wire:target="submit">Login</span><span wire:loading wire:target="submit">Signing in...</span>
		</button>
	</div>
	<div class="mt-4 text-center"><a href="{{ route('password.request') }}">Forgot your password?</a></div>
	<div class="mt-6 text-sm-center  ">
		<p class="text-muted"> If you need assistance, contact your administrator</p>
	</div>
	<div class="p-1"
		style="opacity: 0.4;">
		<hr>
	</div>
	<div class="mt-3 d-flex justify-content-between text-sm">
		<a href="{{ route('landing.page') }}"
			class="text-primary text-decoration-underline w-100 text-center">Go
			Back</a>
	</div>
</form>
