@extends('layouts.app')

@section('title', 'Регистрация — Отдых в Карелии')
@section('description', 'Создайте аккаунт для публикации постов, комментирования и оценки мест в Карелии.')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Заголовок -->
                    <div class="text-center mb-4">
                        <div class="auth-logo mb-3">
                            <i class="fas fa-mountain text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Присоединяйтесь к нам!</h2>
                        <p class="text-muted">Создайте аккаунт и поделитесь своими впечатлениями</p>
                    </div>

                    <!-- Форма регистрации -->
                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf
                        <!-- Honeypot поля -->
                        <div style="position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden;">
                            <label for="website">Website</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                            <input type="hidden" name="form_started_at" value="{{ time() }}">
                        </div>

                        <!-- Имя -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>Имя
                            </label>
                            <input id="name" 
                                   type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autocomplete="name" 
                                   autofocus
                                   placeholder="Введите ваше имя">
                            @error('name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

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
                                       autocomplete="new-password"
                                       placeholder="Создайте пароль">
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
                                       placeholder="Повторите пароль">
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

                        <!-- Согласие с условиями -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" 
                                       type="checkbox" 
                                       name="terms" 
                                       id="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    Я согласен с 
                                    <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">
                                        условиями использования
                                    </a>
                                    и 
                                    <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#privacyModal">
                                        политикой конфиденциальности
                                    </a>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Кнопка регистрации -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="registerBtn" disabled>
                                <i class="fas fa-user-plus me-2"></i>Создать аккаунт
                            </button>
                        </div>
                    </form>

                    <!-- Разделитель -->
                    <div class="text-center mb-4">
                        <hr class="my-4">
                        <span class="text-muted bg-white px-3">или</span>
                    </div>

                    <!-- Вход -->
                    <div class="text-center">
                        <p class="text-muted mb-0">Уже есть аккаунт?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary mt-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Войти
                        </a>
                    </div>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Регистрируясь, вы соглашаетесь с нашими условиями использования
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Модальные окна для условий -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Условия использования</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Общие положения</h6>
                <p>Используя наш сайт, вы соглашаетесь с данными условиями использования.</p>
                
                <h6>2. Пользовательский контент</h6>
                <p>Вы несете ответственность за размещаемый контент и обязуетесь не нарушать права третьих лиц.</p>
                
                <h6>3. Запрещенные действия</h6>
                <p>Запрещается размещение спама, оскорбительного контента и нарушение авторских прав.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="privacyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Политика конфиденциальности</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Сбор данных</h6>
                <p>Мы собираем только необходимые данные для предоставления услуг.</p>
                
                <h6>2. Использование данных</h6>
                <p>Ваши данные используются исключительно для функционирования сайта и не передаются третьим лицам.</p>
                
                <h6>3. Безопасность</h6>
                <p>Мы принимаем все необходимые меры для защиты ваших персональных данных.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<!-- Стили для страницы регистрации -->
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

    .btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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

<!-- JavaScript для страницы регистрации -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordMatch = document.getElementById('passwordMatch');
    const termsCheckbox = document.getElementById('terms');
    const registerBtn = document.getElementById('registerBtn');
    
    // Отладочная информация
    console.log('Элементы найдены:', {
        passwordInput: !!passwordInput,
        passwordConfirmInput: !!passwordConfirmInput,
        passwordMatch: !!passwordMatch
    });

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


    // Функция проверки совпадения паролей
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;
        
        console.log('Проверка паролей:', { password, confirmPassword, match: password === confirmPassword });
        
        if (confirmPassword.length > 0) {
            const icon = passwordMatch.querySelector('i');
            const small = passwordMatch.querySelector('small');
            
            if (password === confirmPassword) {
                passwordMatch.style.display = 'block';
                icon.className = 'fas fa-check-circle text-success me-1';
                small.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>Пароли совпадают';
            } else {
                passwordMatch.style.display = 'block';
                icon.className = 'fas fa-times-circle text-danger me-1';
                small.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i>Пароли не совпадают';
            }
        } else {
            passwordMatch.style.display = 'none';
        }
    }

    // Проверка совпадения паролей при изменении основного пароля
    if (passwordInput) {
        ['input', 'keyup', 'paste'].forEach(eventType => {
            passwordInput.addEventListener(eventType, function() {
                const password = this.value;
                const strength = checkPasswordStrength(password);
                
                if (password.length > 0) {
                    passwordStrength.style.display = 'block';
                    updatePasswordStrength(strength);
                } else {
                    passwordStrength.style.display = 'none';
                }
                
                // Проверяем совпадение паролей при изменении основного пароля
                setTimeout(checkPasswordMatch, 10); // Небольшая задержка для обновления значения
            });
        });
    }

    // Проверка совпадения паролей при изменении подтверждения пароля
    if (passwordConfirmInput) {
        ['input', 'keyup', 'paste'].forEach(eventType => {
            passwordConfirmInput.addEventListener(eventType, function() {
                setTimeout(checkPasswordMatch, 10); // Небольшая задержка для обновления значения
            });
        });
    }

    // Проверка условий использования
    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', function() {
            updateRegisterButton();
        });
    }

    // Обновление кнопки регистрации
    function updateRegisterButton() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;
        const termsAccepted = termsCheckbox.checked;
        
        const isValid = password.length >= 8 && 
                       password === confirmPassword && 
                       termsAccepted;
        
        registerBtn.disabled = !isValid;
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
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = passwordInput.value;
            const confirmPassword = passwordConfirmInput.value;
            const terms = termsCheckbox.checked;
            
            if (!name || !email || !password || !confirmPassword || !terms) {
                e.preventDefault();
                
                if (!name) showFieldError('name', 'Имя обязательно для заполнения');
                if (!email) showFieldError('email', 'Email обязателен для заполнения');
                if (!password) showFieldError('password', 'Пароль обязателен для заполнения');
                if (!confirmPassword) showFieldError('password_confirmation', 'Подтверждение пароля обязательно');
                if (!terms) showFieldError('terms', 'Необходимо согласиться с условиями использования');
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