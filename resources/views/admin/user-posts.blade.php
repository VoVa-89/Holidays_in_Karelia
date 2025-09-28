@extends('layouts.admin')

@section('title', 'Посты пользователя')
@section('description', 'Просмотр всех постов пользователя в административной панели.')

@section('content')
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-user me-2"></i>Посты пользователя: {{ $user->name }}
                        </h1>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Назад к панели
                        </a>
                        <a href="{{ route('admin.users.list') }}" class="btn btn-outline-primary">
                            <i class="fas fa-users me-2"></i>Все пользователи
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Информация о пользователе -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-user fa-2x text-white"></i>
                                    </div>
                                    <h5 class="mb-1">{{ $user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-primary mb-0">{{ $posts->total() }}</h4>
                                            <small class="text-muted">Всего постов</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-success mb-0">{{ $posts->where('status', 'published')->count() }}</h4>
                                            <small class="text-muted">Опубликовано</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-warning mb-0">{{ $posts->where('status', 'moderation')->count() }}</h4>
                                            <small class="text-muted">На модерации</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-danger mb-0">{{ $posts->where('status', 'rejected')->count() }}</h4>
                                            <small class="text-muted">Отклонено</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список постов -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Все посты пользователя
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($posts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Пост</th>
                                            <th>Категория</th>
                                            <th>Статус</th>
                                            <th>Просмотры</th>
                                            <th>Рейтинг</th>
                                            <th>Дата создания</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($posts as $post)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($post->photos->count() > 0)
                                                            <img src="{{ asset($post->photos->first()->photo_path) }}" 
                                                                 alt="{{ $post->title }}" 
                                                                 class="rounded me-3" 
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                                                 style="width: 50px; height: 50px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">
                                                                    {{ Str::limit($post->title, 40) }}
                                                                </a>
                                                            </h6>
                                                            <small class="text-muted">{{ Str::limit($post->address, 30) }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $post->category->name }}</span>
                                                </td>
                                                <td>
                                                    @switch($post->status)
                                                        @case('published')
                                                            <span class="badge bg-success">Опубликован</span>
                                                            @break
                                                        @case('moderation')
                                                            <span class="badge bg-warning">На модерации</span>
                                                            @break
                                                        @case('draft')
                                                            <span class="badge bg-secondary">Черновик</span>
                                                            @break
                                                        @case('rejected')
                                                            <span class="badge bg-danger">Отклонен</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $post->views }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $post->rating)
                                                                <i class="fas fa-star text-warning small"></i>
                                                            @else
                                                                <i class="far fa-star text-muted small"></i>
                                                            @endif
                                                        @endfor
                                                        <small class="text-muted ms-1">{{ number_format($post->rating, 1) }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $post->created_at->format('d.m.Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-outline-primary btn-sm" title="Просмотр">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('posts.edit', $post->slug) }}" class="btn btn-outline-warning btn-sm" title="Редактировать">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if($post->status === 'moderation')
                                                            <form method="POST" action="{{ route('admin.posts.approve', $post->id) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm" title="Одобрить">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('admin.posts.reject', $post->id) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Отклонить">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
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
                                {{ $posts->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">У пользователя нет постов</h5>
                                <p class="text-muted">Этот пользователь еще не создал ни одного поста.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
