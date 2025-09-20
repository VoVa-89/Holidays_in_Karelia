@extends('layouts.app')

@section('title', 'Сброс пароля — Отдых в Карелии')
@section('description', 'Создайте новый пароль для вашего аккаунта.')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Заголовок -->
                    <div class="text-center mb-4">
                        <div class="auth-logo mb-3">
                            <i class="fas fa-lock text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Новый пароль</h2>
                        <p class="text-muted">Создайте новый пароль для вашего аккаунта</p>
                    </div>

                    <!-- Форма сброса пароля -->
                    <form method="POST" action="{{ route('password.update') }}" novalidate>
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email адрес
                            </label>
                            <input id="email" 
                                   type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ $email ?? old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   readonly>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Новый пароль -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Новый пароль
                            </label>
                            <div class="input-group">
                                <input id="password" 
                                       type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Создайте новый пароль">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="password-strength-text text-muted"></small>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Подтверждение пароля -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-2"></i>Подтвердите пароль
                            </label>
                            <div class="input-group">
                                <input id="password_confirmation" 
                                       type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Повторите новый пароль">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-match mt-2" id="passwordMatch" style="display: none;">
                                <small class="text-muted">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Пароли совпадают
                                </small>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Кнопка сброса -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="resetBtn" disabled>
                                <i class="fas fa-save me-2"></i>Сохранить пароль
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
                        <p class="text-muted mb-2">Вспомнили старый пароль?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Войти
                        </a>
                    </div>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Новый пароль должен содержать минимум 8 символов
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Стили для страницы сброса пароля -->
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

    .form-control[readonly] {
        background-color: var(--bs-light);
        opacity: 0.8;
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

    .btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .password-strength .progress-bar {
        transition: all 0.3s ease;
    }

    .password-strength.weak .progress-bar {
        background-color: var(--bs-danger);
    }

    .password-strength.medium .progress-bar {
        background-color: var(--bs-warning);
    }

    .password-strength.strong .progress-bar {
        background-color: var(--bs-success);
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

<!-- JavaScript для страницы сброса пароля -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordMatch = document.getElementById('passwordMatch');
    const resetBtn = document.getElementById('resetBtn');

    // Переключение видимости паролей
    function setupPasswordToggle(toggleId, inputId) {
        const toggle = document.getElementById(toggleId);
        const input = document.getElementById(inputId);
        
        if (toggle && input) {
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }
    }

    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('togglePasswordConfirm', 'password_confirmation');

    // Проверка силы пароля
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            
            if (password.length > 0) {
                passwordStrength.style.display = 'block';
                updatePasswordStrength(strength);
            } else {
                passwordStrength.style.display = 'none';
            }
            
            updateResetButton();
        });
    }

    // Проверка совпадения паролей
    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    passwordMatch.style.display = 'block';
                    passwordMatch.querySelector('i').className = 'fas fa-check-circle text-success me-1';
                    passwordMatch.querySelector('small').textContent = 'Пароли совпадают';
                } else {
                    passwordMatch.style.display = 'block';
                    passwordMatch.querySelector('i').className = 'fas fa-times-circle text-danger me-1';
                    passwordMatch.querySelector('small').textContent = 'Пароли не совпадают';
                }
            } else {
                passwordMatch.style.display = 'none';
            }
            
            updateResetButton();
        });
    }

    // Обновление кнопки сброса
    function updateResetButton() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;
        
        const isValid = password.length >= 8 && password === confirmPassword;
        resetBtn.disabled = !isValid;
    }

    // Проверка силы пароля
    function checkPasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;
        
        if (score < 3) return 'weak';
        if (score < 4) return 'medium';
        return 'strong';
    }

    // Обновление индикатора силы пароля
    function updatePasswordStrength(strength) {
        const progressBar = passwordStrength.querySelector('.progress-bar');
        const strengthText = passwordStrength.querySelector('.password-strength-text');
        
        passwordStrength.className = `password-strength mt-2 ${strength}`;
        
        switch (strength) {
            case 'weak':
                progressBar.style.width = '33%';
                strengthText.textContent = 'Слабый пароль';
                break;
            case 'medium':
                progressBar.style.width = '66%';
                strengthText.textContent = 'Средний пароль';
                break;
            case 'strong':
                progressBar.style.width = '100%';
                strengthText.textContent = 'Сильный пароль';
                break;
        }
    }

    // Валидация формы
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = passwordConfirmInput.value;
            
            if (password.length < 8 || password !== confirmPassword) {
                e.preventDefault();
                
                if (password.length < 8) {
                    showFieldError('password', 'Пароль должен содержать минимум 8 символов');
                }
                if (password !== confirmPassword) {
                    showFieldError('password_confirmation', 'Пароли не совпадают');
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