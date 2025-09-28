@extends('layouts.admin')

@section('title', 'Список пользователей')
@section('description', 'Просмотр всех пользователей системы в административной панели.')

@section('content')
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-users me-2"></i>Список пользователей
                        </h1>
                        <p class="text-muted mb-0">Всего пользователей: {{ $users->total() }}</p>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Назад к панели
                        </a>
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-warning">
                                <i class="fas fa-cog me-2"></i>Управление ролями
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Поиск и фильтры -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.users.list') }}" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Поиск пользователей</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Поиск по имени или email...">
                            </div>
                            @if(Auth::user()->isSuperAdmin())
                                <div class="col-md-3">
                                    <label for="role" class="form-label">Роль</label>
                                    <select class="form-select" id="role" name="role">
                                        <option value="">Все роли</option>
                                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Пользователь</option>
                                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Администратор</option>
                                        <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Супер-администратор</option>
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Поиск
                                </button>
                                <a href="{{ route('admin.users.list') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Сбросить
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список пользователей -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Все пользователи
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Пользователь</th>
                                            <th>Роль</th>
                                            <th>Активность</th>
                                            <th>Статистика</th>
                                            <th>Дата регистрации</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ $user->name }}</h6>
                                                            <small class="text-muted">{{ $user->email }}</small>
                                                            @if($user->id === auth()->id())
                                                                <br><small class="text-primary">(Вы)</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $roleClasses = [
                                                            'user' => 'bg-secondary',
                                                            'admin' => 'bg-warning',
                                                            'superadmin' => 'bg-danger'
                                                        ];
                                                        $roleNames = [
                                                            'user' => 'Пользователь',
                                                            'admin' => 'Администратор',
                                                            'superadmin' => 'Супер-администратор'
                                                        ];
                                                    @endphp
                                                    <span class="badge {{ $roleClasses[$user->role] }}">
                                                        {{ $roleNames[$user->role] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small class="text-muted">
                                                            Последний вход: {{ $user->updated_at->diffForHumans() }}
                                                        </small>
                                                        <small class="text-muted">
                                                            Регистрация: {{ $user->created_at->format('d.m.Y') }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <span class="badge bg-primary" title="Посты">{{ $user->posts_count }}</span>
                                                        <span class="badge bg-info" title="Комментарии">{{ $user->comments_count }}</span>
                                                        <span class="badge bg-warning" title="Рейтинги">{{ $user->ratings_count }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $user->created_at->format('d.m.Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.users.posts', $user->id) }}" class="btn btn-outline-primary btn-sm" title="Посты пользователя">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(Auth::user()->isSuperAdmin())
                                                            <a href="{{ route('admin.users') }}?user={{ $user->id }}" class="btn btn-outline-warning btn-sm" title="Управление ролями">
                                                                <i class="fas fa-cog"></i>
                                                            </a>
                                                            @if($user->id !== Auth::id() && $user->role !== 'superadmin')
                                                                <button type="button" 
                                                                        class="btn btn-outline-danger btn-sm" 
                                                                        title="Удалить пользователя"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#deleteUserModal{{ $user->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Пагинация -->
                            <div class="card-footer">
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Пользователи не найдены</h5>
                                <p class="text-muted">Попробуйте изменить параметры поиска.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальные окна подтверждения удаления -->
    @foreach($users as $user)
        @if(Auth::user()->isSuperAdmin() && $user->id !== Auth::id() && $user->role !== 'superadmin')
            <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">
                                <i class="fas fa-exclamation-triangle me-2"></i>Удаление пользователя
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Внимание!</strong> Это действие необратимо.
                            </div>
                            <p>Вы действительно хотите удалить пользователя <strong>{{ $user->name }}</strong>?</p>
                            <p class="text-muted small">
                                При удалении пользователя будут также удалены:
                            </p>
                            <ul class="text-muted small">
                                <li>Все посты пользователя ({{ $user->posts_count ?? 0 }})</li>
                                <li>Все комментарии пользователя ({{ $user->comments_count ?? 0 }})</li>
                                <li>Все рейтинги пользователя ({{ $user->ratings_count ?? 0 }})</li>
                                <li>Все фотографии пользователя</li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Отмена
                            </button>
                            <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Удалить пользователя
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
