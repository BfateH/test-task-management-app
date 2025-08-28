@extends('layouts.app')

@section('content')
    <div class="register-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card register-card">
                    <div class="card-body p-5">
                        <!-- Логотип и заголовок -->
                        <div class="text-center mb-4">
                            <div class="register-logo mb-3">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <h2 class="register-title">{{ __('Создать аккаунт') }}</h2>
                            <p class="text-muted">{{ __('Заполните форму для регистрации') }}</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Поле имени -->
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Имя') }}</label>
                                <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                           placeholder="Введите ваше имя">
                                </div>
                                @error('name')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <!-- Поле email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email адрес') }}</label>
                                <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" required autocomplete="email"
                                           placeholder="Введите ваш email">
                                </div>
                                @error('email')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <!-- Поле пароля -->
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Пароль') }}</label>
                                <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                           name="password" required autocomplete="new-password" placeholder="Придумайте пароль">
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

                            <!-- Подтверждение пароля -->
                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">{{ __('Подтвердите пароль') }}</label>
                                <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                    <input id="password-confirm" type="password" class="form-control"
                                           name="password_confirmation" required autocomplete="new-password"
                                           placeholder="Повторите пароль">
                                    <button class="btn btn-outline-secondary toggle-confirm-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Кнопка регистрации -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-register">
                                    <i class="bi bi-person-plus me-2"></i>{{ __('Зарегистрироваться') }}
                                </button>
                            </div>

                            <!-- Разделитель -->
                            <div class="divider d-flex align-items-center my-4">
                                <p class="text-center mx-3 mb-0 text-muted">или</p>
                            </div>

                            <!-- Ссылка на вход -->
                            @if (Route::has('login'))
                                <div class="text-center">
                                    <p class="mb-0">{{ __('Уже есть аккаунт?') }}
                                        <a href="{{ route('login') }}" class="login-link">{{ __('Войти') }}</a>
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
        .register-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .register-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: none;
            overflow: hidden;
        }

        .register-logo {
            font-size: 3.5rem;
            color: #667eea;
        }

        .register-title {
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

        .btn-register {
            background-color: #667eea;
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-register:hover {
            background-color: #5a67d8;
            transform: translateY(-2px);
        }

        .login-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link:hover {
            text-decoration: underline;
            color: #5a67d8;
        }

        .toggle-password,
        .toggle-confirm-password {
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

        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }

        @media (max-width: 576px) {
            .register-container {
                padding: 10px;
            }

            .register-card {
                border-radius: 10px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Функция для переключения видимости пароля
            const togglePassword = document.querySelector('.toggle-password');
            const password = document.querySelector('#password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);

                    // Меняем иконку
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

            const toggleConfirmPassword = document.querySelector('.toggle-confirm-password');
            const passwordConfirm = document.querySelector('#password-confirm');

            if (toggleConfirmPassword && passwordConfirm) {
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordConfirm.setAttribute('type', type);

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

            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password-confirm');

            if (passwordInput && confirmPasswordInput) {
                passwordInput.addEventListener('input', validatePassword);
                confirmPasswordInput.addEventListener('input', validatePassword);
            }

            function validatePassword() {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('Пароли не совпадают');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            }
        });
    </script>
@endsection
