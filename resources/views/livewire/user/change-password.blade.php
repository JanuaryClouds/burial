<form wire:submit="save" class="d-flex flex-column gap-4">
    @if (auth()->user()->password_change_required)
        <x-callout class="border-warning bg-light-warning">Change your initial password before continuing.</x-callout>
    @endif
    <p>Use at least 12 characters with uppercase and lowercase letters, a number, and a symbol. Spaces are allowed. Maximum 72 bytes.</p>
    <x-form.password name="currentPassword" wire:model="currentPassword" label="Current Password" autocomplete="current-password" />
    <x-form.password name="newPassword" wire:model="newPassword" label="New Password" />
    <x-form.password name="confirmNewPassword" wire:model="confirmNewPassword" label="Confirm New Password" />
    <div><button class="btn btn-primary" type="submit" wire:loading.attr="disabled">Change Password</button></div>
</form>
