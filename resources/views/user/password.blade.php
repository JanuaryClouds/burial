<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>@include('partials.page.head')</head>
<body>
    <main class="container py-10">
        <x-card>
            <x-slot:header>Change Your Password</x-slot:header>
            <livewire:user.change-password />
        </x-card>
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-light">Sign Out</button>
        </form>
    </main>
    @include('partials.page.scripts')
</body>
</html>
