@extends('layouts.app')

@section('title', 'Редактировать профиль')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-edit me-2"></i>Редактировать профиль
                </h1>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Назад к профилю
                </a>
            </div>

            <!-- Форма редактирования -->
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Имя
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Информация о роли (только для просмотра) -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-shield-alt me-1"></i>Роль
                                </label>
                                <div class="form-control-plaintext">
                                    @if($user->role === 'superadmin')
                                        <span class="badge bg-danger">Супер-администратор</span>
                                    @elseif($user->role === 'admin')
                                        <span class="badge bg-warning">Администратор</span>
                                    @else
                                        <span class="badge bg-secondary">Пользователь</span>
                                    @endif
                                </div>
                                <small class="text-muted">Роль может быть изменена только супер-администратором</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Дата регистрации
                                </label>
                                <div class="form-control-plaintext">
                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <!-- Статистика -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-chart-bar me-1"></i>Статистика
                                </label>
                                <div class="row">
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 text-primary mb-0">{{ $user->posts()->count() }}</div>
                                            <small class="text-muted">Постов</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 text-success mb-0">{{ $user->posts()->where('status', 'published')->count() }}</div>
                                            <small class="text-muted">Опубликовано</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 text-info mb-0">{{ \App\Models\Comment::whereIn('post_id', $user->posts()->pluck('id'))->count() }}</div>
                                            <small class="text-muted">Комментариев к постам</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 text-warning mb-0">{{ \App\Models\Rating::whereIn('post_id', $user->posts()->pluck('id'))->count() }}</div>
                                            <small class="text-muted">Оценок постов</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-key me-1"></i>Изменить пароль
                                </a>
                                <a href="{{ route('profile.settings') }}" class="btn btn-outline-info">
                                    <i class="fas fa-cog me-1"></i>Настройки
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
