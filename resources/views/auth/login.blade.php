<x-auth-layout>
    <x-slot name="title">Login</x-slot>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" class="form-control border px-2 @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
                <span class="invalid-feedback text-white" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Kata Sandi') }}</label>
            <input id="password" type="password"
                class="form-control border px-2 @error('password') is-invalid @enderror" name="password" required
                autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback text-white" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="mb-3 d-flex justify-content-between">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                    {{ old('remember') ? 'checked' : '' }}>

                <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div>
            <a href="{{ route('password.request') }}" class="text-white text-decoration-none">
                Reset Password
            </a>
        </div>

        <div class="mb-0 d-grid">
            <button type="submit" class="btn btn-outline-light">
                {{ __('Login') }}
            </button>

            <div class="mt-2 text-center">
                Belum punya akun?
                <a class="fw-bold text-white" href="{{ route('register') }}">
                    Buat akun!
                </a>
            </div>
        </div>
    </form>
</x-auth-layout>
