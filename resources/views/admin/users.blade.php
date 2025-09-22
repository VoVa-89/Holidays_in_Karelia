@extends('layouts.admin')

@section('title', 'Управление пользователями')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Управление пользователями</h1>
                <div class="text-muted">
                    <i class="fas fa-users me-1"></i>
                    Всего пользователей: {{ $users->total() }}
                </div>
            </div>

            <!-- Фильтры и поиск -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Поиск</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Поиск по имени или email...">
                        </div>
                        <div class="col-md-3">
                            <label for="role" class="form-label">Роль</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Все роли</option>
                                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Пользователь</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Администратор</option>
                                <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Супер-администратор</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Поиск
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Сбросить
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Список пользователей -->
            <div class="card">
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Пользователь</th>
                                        <th>Email</th>
                                        <th>Роль</th>
                                        <th>Дата регистрации</th>
                                        <th>Постов</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">#{{ $user->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $user->name }}</div>
                                                        @if($user->id === auth()->id())
                                                            <small class="text-muted">(Вы)</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                    {{ $user->email }}
                                                </a>
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
                                                <small class="text-muted">
                                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $user->posts_count ?? $user->posts()->count() }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->id !== auth()->id())
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                                type="button" 
                                                                data-bs-toggle="dropdown" 
                                                                aria-expanded="false">
                                                            <i class="fas fa-cog me-1"></i>Изменить роль
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @foreach($roles as $role)
                                                                @if($role !== $user->role)
                                                                    <li>
                                                                        <form method="POST" 
                                                                              action="{{ route('admin.users.update-role', $user->id) }}" 
                                                                              class="d-inline">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <input type="hidden" name="role" value="{{ $role }}">
                                                                            <button type="submit" 
                                                                                    class="dropdown-item text-{{ $role === 'superadmin' ? 'danger' : ($role === 'admin' ? 'warning' : 'secondary') }}"
                                                                                    onclick="return confirm('Вы уверены, что хотите изменить роль пользователя \'{{ $user->name }}\' на \'{{ $roleNames[$role] }}\'?')">
                                                                                <i class="fas fa-user-{{ $role === 'superadmin' ? 'shield' : ($role === 'admin' ? 'cog' : 'user') }} me-2"></i>
                                                                                Сделать {{ $roleNames[$role] }}
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-lock me-1"></i>Нельзя изменить
                                                    </span>
                                                @endif
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
                            <p class="text-muted">Попробуйте изменить параметры поиска или фильтры.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: 600;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection
