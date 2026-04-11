@extends('layouts.admin')

@section('title', 'Модерация отзывов')
@section('description', 'Гостевые отзывы, ожидающие проверки.')

@section('content')
	<div class="container-fluid my-4">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h1 class="h3 mb-0"><i class="fas fa-comments text-primary me-2"></i>Модерация отзывов</h1>
			<div class="btn-group">
				<a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
					<i class="fas fa-arrow-left me-2"></i>К панели
				</a>
				<a href="{{ route('admin.moderation') }}" class="btn btn-outline-primary">
					<i class="fas fa-clipboard-check me-2"></i>Посты
				</a>
			</div>
		</div>

		<div class="card">
			<div class="card-body">
				@forelse($comments as $comment)
					<div class="border rounded p-3 mb-3">
						<div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
							<div>
								<strong>{{ $comment->guest_display_name ?? 'Гость' }}</strong>
								<span class="text-muted small ms-2">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
							</div>
							<div class="d-flex gap-2">
								<form method="POST" action="{{ route('admin.comments.approve', $comment->id) }}" class="d-inline">
									@csrf
									<button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Одобрить</button>
								</form>
								<form method="POST" action="{{ route('admin.comments.reject', $comment->id) }}" class="d-inline" onsubmit="return confirm('Отклонить этот отзыв?');">
									@csrf
									<button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times me-1"></i>Отклонить</button>
								</form>
							</div>
						</div>
						<p class="mb-2">{{ $comment->content }}</p>
						<small class="text-muted">
							Пост: <a href="{{ route('posts.show', $comment->post->slug) }}" target="_blank" rel="noopener">{{ $comment->post->title }}</a>
						</small>
					</div>
				@empty
					<p class="text-muted mb-0">Нет отзывов в очереди.</p>
				@endforelse

				<div class="mt-3">
					{{ $comments->links() }}
				</div>
			</div>
		</div>
	</div>
@endsection
