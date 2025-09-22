@extends('layouts.app')

@section('title', 'Изменить пароль')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-key me-2"></i>Изменить пароль
                </h1>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Назад к профилю
                </a>
            </div>

            <!-- Форма изменения пароля -->
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Текущий пароль
                            </label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-key me-1"></i>Новый пароль
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Пароль должен содержать минимум 8 символов
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-check me-1"></i>Подтвердите новый пароль
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>

                        <!-- Индикатор силы пароля -->
                        <div class="mb-3">
                            <label class="form-label">Сила пароля:</label>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" id="password-strength" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="password-strength-text">Введите пароль</small>
                        </div>

                        <!-- Рекомендации по паролю -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-1"></i>Рекомендации по паролю:
                            </h6>
                            <ul class="mb-0">
                                <li>Используйте минимум 8 символов</li>
                                <li>Включите заглавные и строчные буквы</li>
                                <li>Добавьте цифры и специальные символы</li>
                                <li>Не используйте личную информацию</li>
                            </ul>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Отмена
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Изменить пароль
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('password-strength-text');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        strengthBar.style.width = strength.score + '%';
        strengthBar.className = 'progress-bar ' + strength.color;
        strengthText.textContent = strength.text;
    });

    function calculatePasswordStrength(password) {
        let score = 0;
        let text = 'Очень слабый';
        let color = 'bg-danger';

        if (password.length >= 8) score += 20;
        if (password.length >= 12) score += 10;
        if (/[a-z]/.test(password)) score += 10;
        if (/[A-Z]/.test(password)) score += 10;
        if (/[0-9]/.test(password)) score += 10;
        if (/[^A-Za-z0-9]/.test(password)) score += 10;
        if (password.length >= 16) score += 10;
        if (password.length >= 20) score += 10;

        if (score >= 80) {
            text = 'Очень сильный';
            color = 'bg-success';
        } else if (score >= 60) {
            text = 'Сильный';
            color = 'bg-info';
        } else if (score >= 40) {
            text = 'Средний';
            color = 'bg-warning';
        } else if (score >= 20) {
            text = 'Слабый';
            color = 'bg-danger';
        } else if (password.length > 0) {
            text = 'Очень слабый';
            color = 'bg-danger';
        } else {
            text = 'Введите пароль';
            color = 'bg-secondary';
        }

        return { score, text, color };
    }
});
</script>
@endsection
