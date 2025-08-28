@extends('layouts.app')

@section('content')
    <div class="login-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="login-logo mb-3">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <h2 class="login-title">{{ __('Добро пожаловать') }}</h2>
                            <p class="text-muted">{{ __('Войдите в свой аккаунт') }}</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email адрес') }}</label>
                                <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                           placeholder="Введите ваш email">
                                </div>
                                @error('email')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label">{{ __('Пароль') }}</label>
                                    @if (Route::has('password.request'))
                                        <a class="forgot-link" href="{{ route('password.request') }}">
                                            {{ __('Забыли пароль?') }}
                                        </a>
                                    @endif
                                </div>
                                <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                           name="password" required autocomplete="current-password" placeholder="Введите ваш пароль">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Запомнить меня') }}
                                </label>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Войти') }}
                                </button>
                            </div>

                            <div class="divider d-flex align-items-center my-4">
                                <p class="text-center mx-3 mb-0 text-muted">или</p>
                            </div>

                            @if (Route::has('register'))
                                <div class="text-center">
                                    <p class="mb-0">{{ __('Нет аккаунта?') }}
                                        <a href="{{ route('register') }}" class="register-link">{{ __('Зарегистрироваться') }}</a>
                                    </p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .login-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: none;
            overflow: hidden;
        }

        .login-logo {
            font-size: 3.5rem;
            color: #667eea;
        }

        .login-title {
            font-weight: 600;
            color: #2d3748;
        }

        .input-group-text {
            background-color: #f7fafc;
            border-right: none;
        }

        .form-control {
            border-left: none;
            padding-left: 0;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .btn-login {
            background-color: #667eea;
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: #5a67d8;
            transform: translateY(-2px);
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: #5a67d8;
        }

        .register-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link:hover {
            text-decoration: underline;
            color: #5a67d8;
        }

        .toggle-password {
            border-left: none;
        }

        .divider {
            color: #a0aec0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider::before {
            margin-right: 0.5rem;
        }

        .divider::after {
            margin-left: 0.5rem;
        }

        .card {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 10px;
            }

            .login-card {
                border-radius: 10px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const togglePassword = document.querySelector('.toggle-password');
            const password = document.querySelector('#password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);

                    const icon = this.querySelector('i');
                    if (type === 'password') {
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    } else {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                });
            }
        });
    </script>
@endsection
