@extends('layouts.app')

@section('title', 'Вход — Отдых в Карелии')
@section('description', 'Войдите в свой аккаунт для создания постов, комментирования и оценки мест.')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Заголовок -->
                    <div class="text-center mb-4">
                        <div class="auth-logo mb-3">
                            <i class="fas fa-mountain text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Добро пожаловать!</h2>
                        <p class="text-muted">Войдите в свой аккаунт</p>
                    </div>

                    <!-- Форма входа -->
                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email адрес
                            </label>
                            <input id="email" 
                                   type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="Введите ваш email">
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Пароль -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Пароль
                            </label>
                            <div class="input-group">
                                <input id="password" 
                                       type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Введите пароль">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    <i class="fas fa-check me-1"></i>Запомнить меня
                                </label>
                            </div>
                        </div>

                        <!-- Кнопка входа -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Войти
                            </button>
                        </div>

                        <!-- Забыли пароль -->
                        <div class="text-center mb-4">
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="fas fa-key me-1"></i>Забыли пароль?
                                </a>
                            @endif
                        </div>
                    </form>

                    <!-- Разделитель -->
                    <div class="text-center mb-4">
                        <hr class="my-4">
                        <span class="text-muted bg-white px-3">или</span>
                    </div>

                    <!-- Регистрация -->
                    <div class="text-center">
                        <p class="text-muted mb-0">Еще нет аккаунта?</p>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary mt-2">
                            <i class="fas fa-user-plus me-2"></i>Создать аккаунт
                        </a>
                    </div>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Ваши данные защищены и не передаются третьим лицам
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Стили для страницы входа -->
<style>
    .auth-logo {
        animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .card {
        border-radius: 1rem;
        backdrop-filter: blur(10px);
    }

    .form-control {
        border-radius: 0.75rem;
        border: 2px solid var(--bs-border-color);
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        transform: translateY(-2px);
    }

    .form-control.is-invalid {
        border-color: var(--bs-danger);
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .btn {
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn:active {
        transform: translateY(0);
    }

    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .form-check-label {
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .form-check-label:hover {
        color: var(--bs-primary);
    }

    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    /* Адаптивность */
    @media (max-width: 576px) {
        .card-body {
            padding: 2rem 1.5rem !important;
        }
        
        .auth-logo i {
            font-size: 2.5rem !important;
        }
        
        h2 {
            font-size: 1.5rem;
        }
    }
</style>

<!-- JavaScript для страницы входа -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение видимости пароля
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }

    // Анимация при фокусе на полях
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentNode.classList.remove('focused');
            }
        });
    });

    // Валидация формы
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                
                if (!email) {
                    showFieldError('email', 'Email обязателен для заполнения');
                }
                if (!password) {
                    showFieldError('password', 'Пароль обязателен для заполнения');
                }
            }
        });
    }

    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        
        field.classList.add('is-invalid');
        if (feedback) {
            feedback.textContent = message;
        }
    }
});
</script>
@endsection