@extends('layouts.app')

@section('title', 'Мой профиль')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-user me-2"></i>Мой профиль
                </h1>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Редактировать профиль
                </a>
            </div>

            <!-- Информация о пользователе -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            @if($user->isAdmin())
                                <span class="badge bg-warning mt-2">
                                    @if($user->isSuperAdmin())
                                        Супер-администратор
                                    @else
                                        Администратор
                                    @endif
                                </span>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h6 class="text-muted mb-3">Информация о профиле</h6>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <strong>Дата регистрации:</strong><br>
                                    <span class="text-muted">{{ $user->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <strong>Последнее обновление:</strong><br>
                                    <span class="text-muted">{{ $user->updated_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <strong>Email подтвержден:</strong><br>
                                    @if($user->email_verified_at)
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Да
                                        </span>
                                    @else
                                        <span class="text-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Нет
                                        </span>
                                    @endif
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <strong>Роль:</strong><br>
                                    <span class="text-muted">
                                        @if($user->role === 'superadmin')
                                            Супер-администратор
                                        @elseif($user->role === 'admin')
                                            Администратор
                                        @else
                                            Пользователь
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Статистика -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Статистика активности
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="h4 text-primary mb-1">{{ $stats['posts_count'] }}</div>
                                <small class="text-muted">Всего постов</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="h4 text-success mb-1">{{ $stats['published_posts'] }}</div>
                                <small class="text-muted">Опубликовано</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="h4 text-warning mb-1">{{ $stats['moderation_posts'] }}</div>
                                <small class="text-muted">На модерации</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="h4 text-secondary mb-1">{{ $stats['draft_posts'] }}</div>
                                <small class="text-muted">Черновики</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="h4 text-danger mb-1">{{ $stats['rejected_posts'] }}</div>
                                <small class="text-muted">Отклонено</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="h4 text-info mb-1">{{ $stats['posts_comments_count'] }}</div>
                                <small class="text-muted">Комментариев к постам</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="h4 text-dark mb-1">{{ $stats['posts_ratings_count'] }}</div>
                                <small class="text-muted">Оценок постов</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="h4 text-secondary mb-1">{{ $stats['comments_count'] + $stats['ratings_count'] }}</div>
                                <small class="text-muted">Мои комментарии и оценки</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Последние посты -->
            @if($recentPosts->count() > 0)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-newspaper me-2"></i>Последние посты
                        </h5>
                        <a href="{{ route('my-posts.index') }}" class="btn btn-sm btn-outline-primary">
                            Все посты
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($recentPosts as $post)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">
                                                {{ $post->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $post->category->name }} • 
                                            {{ $post->created_at->format('d.m.Y H:i') }}
                                        </small>
                                    </div>
                                    <span class="badge 
                                        @if($post->status === 'published') bg-success
                                        @elseif($post->status === 'moderation') bg-warning
                                        @elseif($post->status === 'rejected') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        @if($post->status === 'published') Опубликован
                                        @elseif($post->status === 'moderation') На модерации
                                        @elseif($post->status === 'rejected') Отклонен
                                        @else Черновик
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
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
                        <a href="{{ route('posts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Создать пост
                        </a>
                        <a href="{{ route('my-posts.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-newspaper me-2"></i>Мои посты
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-edit me-2"></i>Редактировать профиль
                        </a>
                        <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                            <i class="fas fa-key me-2"></i>Изменить пароль
                        </a>
                        <a href="{{ route('profile.settings') }}" class="btn btn-outline-info">
                            <i class="fas fa-cog me-2"></i>Настройки
                        </a>
                    </div>
                </div>
            </div>

            <!-- Админ-панель (если пользователь админ) -->
            @if($user->isAdmin())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Администратор
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">
                                <i class="fas fa-tachometer-alt me-2"></i>Панель управления
                            </a>
                            <a href="{{ route('admin.moderation') }}" class="btn btn-outline-warning">
                                <i class="fas fa-gavel me-2"></i>Модерация
                            </a>
                            @if($user->isSuperAdmin())
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-users me-2"></i>Управление пользователями
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection
