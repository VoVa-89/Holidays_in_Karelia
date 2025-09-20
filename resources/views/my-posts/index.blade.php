@extends('layouts.app')

@section('title', 'Мои посты — Отдых в Карелии')
@section('description', 'Управление вашими постами: просмотр статусов, причин отклонения и редактирование.')

@section('content')
	<div class="container my-4">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h1 class="h3 mb-0"><i class="fas fa-newspaper text-primary me-2"></i>Мои посты</h1>
			<a href="{{ route('posts.create') }}" class="btn btn-primary">
				<i class="fas fa-plus me-2"></i>Создать пост
			</a>
		</div>

		<!-- Статистика -->
		<div class="row g-3 mb-4">
			<div class="col-6 col-md-2">
				<div class="card text-center">
					<div class="card-body py-3">
						<h5 class="card-title text-primary mb-1">{{ $stats['total'] }}</h5>
						<small class="text-muted">Всего</small>
					</div>
				</div>
			</div>
			<div class="col-6 col-md-2">
				<div class="card text-center">
					<div class="card-body py-3">
						<h5 class="card-title text-success mb-1">{{ $stats['published'] }}</h5>
						<small class="text-muted">Опубликовано</small>
					</div>
				</div>
			</div>
			<div class="col-6 col-md-2">
				<div class="card text-center">
					<div class="card-body py-3">
						<h5 class="card-title text-warning mb-1">{{ $stats['moderation'] }}</h5>
						<small class="text-muted">На модерации</small>
					</div>
				</div>
			</div>
			<div class="col-6 col-md-2">
				<div class="card text-center">
					<div class="card-body py-3">
						<h5 class="card-title text-danger mb-1">{{ $stats['rejected'] }}</h5>
						<small class="text-muted">Отклонено</small>
					</div>
				</div>
			</div>
			<div class="col-6 col-md-2">
				<div class="card text-center">
					<div class="card-body py-3">
						<h5 class="card-title text-secondary mb-1">{{ $stats['draft'] }}</h5>
						<small class="text-muted">Черновики</small>
					</div>
				</div>
			</div>
		</div>

		<!-- Фильтры -->
		<div class="card mb-4">
			<div class="card-body">
				<form method="GET" action="{{ route('my-posts.index') }}" class="row g-3 align-items-end">
					<div class="col-md-4">
						<label for="status" class="form-label">Статус</label>
						<select id="status" name="status" class="form-select">
							<option value="">Все статусы</option>
							<option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Опубликовано</option>
							<option value="moderation" {{ request('status') === 'moderation' ? 'selected' : '' }}>На модерации</option>
							<option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Отклонено</option>
							<option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновики</option>
						</select>
					</div>
					<div class="col-md-6">
						<label for="search" class="form-label">Поиск по названию</label>
						<input type="text" id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Введите название поста...">
					</div>
					<div class="col-md-2">
						<button type="submit" class="btn btn-primary w-100">Применить</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Список постов -->
		@if($posts->count() === 0)
			<div class="text-center py-5">
				<i class="fas fa-newspaper text-muted fa-3x mb-3"></i>
				<h4 class="text-muted">Постов не найдено</h4>
				<p class="text-muted">Создайте свой первый пост о красотах Карелии!</p>
				<a href="{{ route('posts.create') }}" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Создать пост
				</a>
			</div>
		@else
			<div class="row g-4">
				@foreach($posts as $post)
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<div class="row g-3">
									<div class="col-md-8">
										<div class="d-flex align-items-start mb-2">
											<h5 class="card-title mb-0 me-3">{{ $post->title }}</h5>
											@switch($post->status)
												@case('published')
													<span class="badge bg-success">Опубликовано</span>
													@break
												@case('moderation')
													<span class="badge bg-warning text-dark">На модерации</span>
													@break
												@case('rejected')
													<span class="badge bg-danger">Отклонено</span>
													@break
												@case('draft')
													<span class="badge bg-secondary">Черновик</span>
													@break
											@endswitch
										</div>
										
										<p class="text-muted mb-2">
											<i class="fas fa-folder me-1"></i>{{ $post->category->name }}
											<span class="mx-2">•</span>
											<i class="far fa-clock me-1"></i>{{ $post->created_at->format('d.m.Y H:i') }}
											@if($post->status === 'rejected' && $post->rejected_at)
												<span class="mx-2">•</span>
												<i class="fas fa-times-circle me-1"></i>Отклонено {{ $post->rejected_at ? $post->rejected_at->format('d.m.Y H:i') : '' }}
											@endif
										</p>

										@if($post->status === 'rejected' && $post->rejection_reason)
											<div class="alert alert-danger mb-3">
												<h6 class="alert-heading mb-2">
													<i class="fas fa-exclamation-triangle me-2"></i>Причина отклонения:
												</h6>
												<p class="mb-0">{{ $post->rejection_reason }}</p>
											</div>
										@endif

										<p class="card-text">{{ Str::limit(strip_tags($post->description), 150) }}</p>
									</div>
									
									<div class="col-md-4">
										<div class="d-flex flex-column gap-2">
											@if($post->status === 'published')
												<a href="{{ route('posts.show', $post->slug) }}" class="btn btn-outline-primary">
													<i class="fas fa-eye me-2"></i>Просмотр
												</a>
											@endif
											
											@can('update', $post)
												<a href="{{ route('posts.edit', $post->slug) }}" class="btn btn-outline-secondary">
													<i class="fas fa-edit me-2"></i>Редактировать
												</a>
											@endcan

											@if($post->status === 'rejected')
												<a href="{{ route('posts.edit', $post->slug) }}" class="btn btn-warning">
													<i class="fas fa-redo me-2"></i>Исправить и отправить
												</a>
												<form method="POST" action="{{ route('posts.destroy', $post->slug) }}" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите полностью удалить этот пост из базы данных? Это действие необратимо и пост нельзя будет восстановить.');">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-danger">
														<i class="fas fa-trash me-2"></i>Удалить пост
													</button>
												</form>
											@endif

											@if($post->status === 'draft')
												<form method="POST" action="{{ route('posts.update', $post->slug) }}" class="d-inline">
													@csrf
													@method('PUT')
													<input type="hidden" name="status" value="moderation">
													<button type="submit" class="btn btn-success w-100">
														<i class="fas fa-paper-plane me-2"></i>Отправить на модерацию
													</button>
												</form>
												<form method="POST" action="{{ route('posts.destroy', $post->slug) }}" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите полностью удалить этот черновик из базы данных? Это действие необратимо и черновик нельзя будет восстановить.');">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-outline-danger w-100 mt-2">
														<i class="fas fa-trash me-2"></i>Удалить черновик
													</button>
												</form>
											@endif
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>

			<!-- Пагинация -->
			<div class="mt-4">
				{{ $posts->onEachSide(1)->links() }}
			</div>
		@endif
	</div>
@endsection
