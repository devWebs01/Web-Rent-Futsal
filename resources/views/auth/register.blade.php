<x-auth-layout>
    <x-slot name="title">Register</x-slot>

    @if (session('status'))
        <div class="alert alert-light" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Nama') }}</label>

            <input id="name" type="text" class="form-control border px-2 @error('name') is-invalid @enderror"
                name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

            @error('name')
                <span class="invalid-feedback text-white" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" class="form-control border px-2 @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" required>

            @error('email')
                <span class="invalid-feedback text-white" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('No. Telp') }}</label>
            <input id="phone" type="number" class="form-control border px-2 @error('phone') is-invalid @enderror"
                name="phone" value="{{ old('phone') }}" required>

            @error('phone')
                <span class="invalid-feedback text-white" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Kata Sandi') }}</label>
            <input id="password" type="password"
                class="form-control border px-2 @error('password') is-invalid @enderror" name="password" required>

            @error('password')
                <span class="invalid-feedback text-white" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="mb-3">
            <label for="password-confirm" class="form-label">{{ __('Ulangi Kata Sandi') }}</label>
            <input id="password-confirm" type="password" class="form-control border px-2" name="password_confirmation"
                required autocomplete="new-password">

        </div>

        <div class="mb-0 d-grid">
            <button type="submit" class="btn btn-outline-light">
                {{ __('Register') }}
            </button>

            <div class="mt-2 text-center">
                Sudah punya akun?
                <a class="fw-bold text-white" href="{{ route('login') }}">
                    Masuk sekarang!
                </a>
            </div>

        </div>
    </form>
</x-auth-layout>
