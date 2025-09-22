@extends('layouts.app')

@section('title', 'Настройки')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-cog me-2"></i>Настройки
                </h1>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Назад к профилю
                </a>
            </div>

            <!-- Настройки уведомлений -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i>Уведомления
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.settings.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="email_notifications" 
                                   name="email_notifications" 
                                   value="1"
                                   {{ old('email_notifications', $user->email_notifications ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                <strong>Email уведомления</strong>
                                <p class="text-muted mb-0 small">
                                    Получать уведомления о новых комментариях, одобрении/отклонении постов и других важных событиях
                                </p>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="public_profile" 
                                   name="public_profile" 
                                   value="1"
                                   {{ old('public_profile', $user->public_profile ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="public_profile">
                                <strong>Публичный профиль</strong>
                                <p class="text-muted mb-0 small">
                                    Разрешить другим пользователям просматривать ваш профиль и статистику
                                </p>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Сохранить настройки
                        </button>
                    </form>
                </div>
            </div>


            <!-- Настройки аккаунта -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-cog me-2"></i>Управление аккаунтом
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6>Безопасность</h6>
                            <p class="text-muted small">
                                Управляйте безопасностью вашего аккаунта.
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-key me-1"></i>Изменить пароль
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6>Опасная зона</h6>
                            <p class="text-muted small">
                                Необратимые действия с вашим аккаунтом.
                            </p>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-danger btn-sm" type="button" 
                                        onclick="confirmDeleteAccount()">
                                    <i class="fas fa-trash me-1"></i>Удалить аккаунт
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="col-lg-4">
            <!-- Быстрые действия -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Быстрые действия
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.show') }}" class="btn btn-primary">
                            <i class="fas fa-user me-2"></i>Мой профиль
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Редактировать профиль
                        </a>
                        <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                            <i class="fas fa-key me-2"></i>Изменить пароль
                        </a>
                        <a href="{{ route('my-posts.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-newspaper me-2"></i>Мои посты
                        </a>
                    </div>
                </div>
            </div>

            <!-- Информация о настройках -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>О настройках
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Здесь вы можете настроить различные аспекты вашего аккаунта, включая уведомления, конфиденциальность и безопасность.
                    </p>
                    <hr>
                    <h6>Текущие настройки:</h6>
                    <ul class="list-unstyled small">
                        <li>
                            <i class="fas fa-bell me-1 text-{{ $user->email_notifications ?? true ? 'success' : 'muted' }}"></i>
                            Email уведомления: {{ $user->email_notifications ?? true ? 'Включены' : 'Отключены' }}
                        </li>
                        <li>
                            <i class="fas fa-globe me-1 text-{{ $user->public_profile ?? false ? 'success' : 'muted' }}"></i>
                            Публичный профиль: {{ $user->public_profile ?? false ? 'Включен' : 'Отключен' }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteAccount() {
    if (confirm('⚠️ ВНИМАНИЕ! Вы уверены, что хотите удалить свой аккаунт?\n\nЭто действие:\n- Полностью удалит ваш аккаунт\n- Удалит ВСЕ ваши посты и фотографии\n- Удалит все ваши комментарии и оценки\n- НЕВОЗМОЖНО отменить\n\nВы действительно хотите продолжить?')) {
        // Создаем форму для отправки DELETE запроса
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("profile.delete") }}';
        
        // Добавляем CSRF токен
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Добавляем метод DELETE
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);
        
        // Добавляем форму в документ и отправляем
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@endsection
