@extends('layouts.admin')

@section('title', 'Модерация постов')
@section('description', 'Модерация контента: просмотр, одобрение и отклонение постов.')

@section('content')
	<div class="container-fluid my-4">
		<div class="row">
			<div class="col-12">
				<div class="d-flex justify-content-between align-items-center mb-4">
					<h1 class="h3 mb-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Модерация постов</h1>
					<div class="btn-group">
						<a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
							<i class="fas fa-arrow-left me-2"></i>Назад к панели
						</a>
						<a href="{{ route('posts.create') }}" class="btn btn-primary">
							<i class="fas fa-plus me-2"></i>Создать пост
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Фильтры -->
		<div class="card mb-4">
			<div class="card-body">
				<form method="GET" action="{{ route('admin.moderation') }}" class="row g-3">
					<div class="col-md-4">
						<label for="category" class="form-label">Категория</label>
						<select name="category" id="category" class="form-select">
							<option value="">Все категории</option>
							@foreach($categories as $category)
								<option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
									{{ $category->name }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4">
						<label for="search" class="form-label">Поиск по названию</label>
						<input type="text" name="search" id="search" class="form-control" 
							   value="{{ request('search') }}" placeholder="Введите название поста...">
					</div>
					<div class="col-md-4">
						<label for="date_from" class="form-label">Дата от</label>
						<input type="date" name="date_from" id="date_from" class="form-control" 
							   value="{{ request('date_from') }}">
					</div>
					<div class="col-12">
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-search me-2"></i>Применить фильтры
						</button>
						<a href="{{ route('admin.moderation') }}" class="btn btn-outline-secondary">
							<i class="fas fa-times me-2"></i>Сбросить
						</a>
					</div>
				</form>
			</div>
		</div>

		<!-- Список постов на модерации -->
		<div class="row">
			@forelse($posts as $post)
				<div class="col-lg-6 col-xl-4 mb-4">
					<div class="card h-100">
						<!-- Используем компонент карточки поста -->
						<x-post-card :post="$post" />
						
						<!-- Дополнительные действия для модерации -->
						<div class="card-footer bg-light">
							<div class="d-grid gap-2">
								<button type="button" class="btn btn-outline-primary btn-sm" 
										data-bs-toggle="modal" data-bs-target="#previewModal{{ $post->id }}">
									<i class="fas fa-eye me-2"></i>Предпросмотр
								</button>
								
								<div class="btn-group">
									<button type="button" class="btn btn-success btn-sm" 
											data-bs-toggle="modal" data-bs-target="#approveModal{{ $post->id }}">
										<i class="fas fa-check me-1"></i>Одобрить
									</button>
									<button type="button" class="btn btn-danger btn-sm" 
											data-bs-toggle="modal" data-bs-target="#rejectModal{{ $post->id }}">
										<i class="fas fa-times me-1"></i>Отклонить
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Модальное окно предпросмотра -->
				<div class="modal fade" id="previewModal{{ $post->id }}" tabindex="-1">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">{{ $post->title }}</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
							</div>
							<div class="modal-body">
								<!-- Галерея изображений -->
								@if($post->photos->count() > 0)
									<div id="carousel{{ $post->id }}" class="carousel slide mb-3" data-bs-ride="carousel">
										<div class="carousel-inner">
											@foreach($post->photos as $index => $photo)
												<div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
													<img src="{{ asset($photo->photo_path) }}" 
														 class="d-block w-100" style="height: 300px; object-fit: cover;" 
														 alt="Фото {{ $index + 1 }}">
												</div>
											@endforeach
										</div>
										@if($post->photos->count() > 1)
											<button class="carousel-control-prev" type="button" 
													data-bs-target="#carousel{{ $post->id }}" data-bs-slide="prev">
												<span class="carousel-control-prev-icon"></span>
											</button>
											<button class="carousel-control-next" type="button" 
													data-bs-target="#carousel{{ $post->id }}" data-bs-slide="next">
												<span class="carousel-control-next-icon"></span>
											</button>
										@endif
									</div>
								@endif

								<!-- Информация о посте -->
								<div class="row g-3 mb-3">
									<div class="col-md-6">
										<strong>Категория:</strong> {{ $post->category->name }}
									</div>
									<div class="col-md-6">
										<strong>Автор:</strong> {{ $post->user->name }}
									</div>
									<div class="col-md-6">
										<strong>Дата создания:</strong> {{ $post->created_at->format('d.m.Y H:i') }}
									</div>
									<div class="col-md-6">
										<strong>Адрес:</strong> {{ $post->address }}
									</div>
								</div>

								<!-- Описание -->
								<div class="mb-3">
									<strong>Описание:</strong>
									<div class="border rounded p-3 mt-2">
										{!! $post->description !!}
									</div>
								</div>

								<!-- Карта -->
								@if($post->latitude && $post->longitude)
									<div class="mb-3">
										<strong>Местоположение:</strong>
										<div id="map{{ $post->id }}" style="height: 200px; border-radius: 8px; margin-top: 8px;"></div>
									</div>
								@endif
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
								<a href="{{ route('posts.edit', $post->slug) }}" class="btn btn-outline-primary">
									<i class="fas fa-edit me-2"></i>Редактировать
								</a>
							</div>
						</div>
					</div>
				</div>

				<!-- Модальное окно одобрения -->
				<div class="modal fade" id="approveModal{{ $post->id }}" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Одобрить пост</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
							</div>
							<form method="POST" action="{{ route('admin.posts.approve', $post->id) }}">
								@csrf
								<div class="modal-body">
									<p>Вы уверены, что хотите одобрить пост <strong>"{{ $post->title }}"</strong>?</p>
									<p class="text-muted">Пост будет опубликован и станет доступен всем пользователям.</p>
									
									<div class="mb-3">
										<label for="approve_reason{{ $post->id }}" class="form-label">Комментарий (необязательно)</label>
										<textarea name="reason" id="approve_reason{{ $post->id }}" class="form-control" 
												  rows="3" placeholder="Добавьте комментарий к одобрению..."></textarea>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
									<button type="submit" class="btn btn-success">
										<i class="fas fa-check me-2"></i>Одобрить
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<!-- Модальное окно отклонения -->
				<div class="modal fade" id="rejectModal{{ $post->id }}" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Отклонить пост</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
							</div>
							<form method="POST" action="{{ route('admin.posts.reject', $post->id) }}">
								@csrf
								<div class="modal-body">
									<p>Вы уверены, что хотите отклонить пост <strong>"{{ $post->title }}"</strong>?</p>
									<p class="text-muted">Пост будет отклонен и автор получит уведомление с причиной.</p>
									
									<div class="mb-3">
										<label for="reject_reason{{ $post->id }}" class="form-label">Причина отклонения <span class="text-danger">*</span></label>
										<textarea name="rejection_reason" id="reject_reason{{ $post->id }}" class="form-control @error('rejection_reason') is-invalid @enderror" 
												  rows="4" placeholder="Укажите подробную причину отклонения поста..." required minlength="10" maxlength="1000">{{ old('rejection_reason') }}</textarea>
										@error('rejection_reason')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
										<div class="form-text">Минимум 10 символов, максимум 1000. Эта причина будет отправлена автору поста.</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
									<button type="submit" class="btn btn-danger">
										<i class="fas fa-times me-2"></i>Отклонить
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			@empty
				<div class="col-12">
					<div class="text-center py-5">
						<i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
						<h4 class="text-muted">Нет постов на модерации</h4>
						<p class="text-muted">Все посты обработаны или еще не отправлены на модерацию.</p>
						<a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
							<i class="fas fa-arrow-left me-2"></i>Вернуться к панели
						</a>
					</div>
				</div>
			@endforelse
		</div>

		<!-- Пагинация -->
		@if($posts->hasPages())
			<div class="d-flex justify-content-center mt-4">
				{{ $posts->links() }}
			</div>
		@endif
	</div>

	@push('scripts')
		<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
		<script>
			// Инициализация карт для модальных окон
			document.addEventListener('DOMContentLoaded', function() {
				@foreach($posts as $post)
					@if($post->latitude && $post->longitude)
						// Карта для поста {{ $post->id }}
						$('#previewModal{{ $post->id }}').on('shown.bs.modal', function() {
							if (typeof ymaps !== 'undefined') {
								ymaps.ready(function() {
									var map{{ $post->id }} = new ymaps.Map('map{{ $post->id }}', {
										center: [{{ $post->latitude }}, {{ $post->longitude }}],
										zoom: 14,
										controls: ['zoomControl']
									});
									
									var marker{{ $post->id }} = new ymaps.Placemark([{{ $post->latitude }}, {{ $post->longitude }}], {
										balloonContent: '{{ $post->address }}'
									}, {
										preset: 'islands#redIcon'
									});
									
									map{{ $post->id }}.geoObjects.add(marker{{ $post->id }});
								});
							}
						});
					@endif
				@endforeach
			});
		</script>
	@endpush
@endsection
