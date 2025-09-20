@extends('layouts.app')

@section('title', 'Восстановление пароля — Отдых в Карелии')
@section('description', 'Восстановите доступ к своему аккаунту, указав email адрес.')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Заголовок -->
                    <div class="text-center mb-4">
                        <div class="auth-logo mb-3">
                            <i class="fas fa-key text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Забыли пароль?</h2>
                        <p class="text-muted">Не волнуйтесь, мы поможем вам восстановить доступ</p>
                    </div>

                    <!-- Информационное сообщение -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Как это работает:</strong><br>
                        Введите ваш email адрес, и мы отправим ссылку для сброса пароля.
                    </div>

                    <!-- Форма восстановления -->
                    <form method="POST" action="{{ route('password.email') }}" novalidate>
                        @csrf

                        <!-- Email -->
                        <div class="mb-4">
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

                        <!-- Кнопка отправки -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="sendResetBtn">
                                <i class="fas fa-paper-plane me-2"></i>Отправить ссылку
                            </button>
                        </div>
                    </form>

                    <!-- Разделитель -->
                    <div class="text-center mb-4">
                        <hr class="my-4">
                        <span class="text-muted bg-white px-3">или</span>
                    </div>

                    <!-- Альтернативные действия -->
                    <div class="text-center">
                        <p class="text-muted mb-2">Вспомнили пароль?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Войти
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-plus me-2"></i>Регистрация
                        </a>
                    </div>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="text-center mt-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted">
                            <i class="fas fa-shield-alt fa-2x mb-2"></i>
                            <div><small>Безопасно</small></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <div><small>Быстро</small></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">
                            <i class="fas fa-envelope fa-2x mb-2"></i>
                            <div><small>На email</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Стили для страницы восстановления пароля -->
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

    .alert {
        border-radius: 0.75rem;
        border: none;
    }

    .alert-info {
        background-color: rgba(13, 202, 240, 0.1);
        color: var(--bs-info);
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
        
        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>

<!-- JavaScript для страницы восстановления пароля -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const sendResetBtn = document.getElementById('sendResetBtn');

    // Валидация email в реальном времени
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email.length > 0) {
                if (emailRegex.test(email)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }

    // Обработка отправки формы
    if (form) {
        form.addEventListener('submit', function(e) {
            const email = emailInput.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!email || !emailRegex.test(email)) {
                e.preventDefault();
                
                if (!email) {
                    showFieldError('email', 'Email обязателен для заполнения');
                } else if (!emailRegex.test(email)) {
                    showFieldError('email', 'Введите корректный email адрес');
                }
            } else {
                // Показываем индикатор загрузки
                sendResetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Отправка...';
                sendResetBtn.disabled = true;
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

    // Анимация при фокусе на поле
    if (emailInput) {
        emailInput.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        emailInput.addEventListener('blur', function() {
            if (!this.value) {
                this.parentNode.classList.remove('focused');
            }
        });
    }
});
</script>
@endsection