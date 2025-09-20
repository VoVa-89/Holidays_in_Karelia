@props(['post'])

<div class="card h-100 shadow-sm post-card">
	<!-- Изображение поста -->
	<div class="card-img-container position-relative">
		@if($post->photos && $post->photos->count() > 0)
			<img src="{{ asset($post->photos->first()->photo_path) }}" 
				 class="card-img-top" 
				 alt="{{ $post->title }}"
				 style="height: 200px; object-fit: cover;">
		@else
			<div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
				 style="height: 200px;">
				<i class="fas fa-image fa-3x text-muted"></i>
			</div>
		@endif
		
		<!-- Категория (бейдж поверх изображения) -->
		<div class="position-absolute top-0 start-0 m-2">
			<span class="badge bg-primary">
				<i class="fas fa-tag me-1"></i>{{ $post->category->name }}
			</span>
		</div>
		
		<!-- Рейтинг (бейдж поверх изображения) -->
		@if($post->rating > 0)
			<div class="position-absolute top-0 end-0 m-2">
				<span class="badge bg-warning text-dark">
					<i class="fas fa-star me-1"></i>{{ number_format($post->rating, 1) }}
				</span>
			</div>
		@endif
	</div>

	<!-- Содержимое карточки -->
	<div class="card-body d-flex flex-column">
		<!-- Заголовок -->
		<h5 class="card-title">
			<a href="{{ route('posts.show', $post->slug) }}" 
			   class="text-decoration-none text-dark stretched-link">
				{{ Str::limit($post->title, 60) }}
			</a>
		</h5>

		<!-- Краткое описание -->
		<p class="card-text text-muted flex-grow-1">
			{{ Str::limit(strip_tags($post->description), 120) }}
		</p>

		<!-- Мета-информация -->
		<div class="card-meta mt-auto">
			<div class="row g-2 align-items-center">
				<!-- Автор -->
				<div class="col-6">
					<div class="d-flex align-items-center">
						<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
							 style="width: 24px; height: 24px;">
							<i class="fas fa-user text-white" style="font-size: 10px;"></i>
						</div>
						<small class="text-muted">{{ $post->user->name }}</small>
					</div>
				</div>
				
				<!-- Дата -->
				<div class="col-6 text-end">
					<small class="text-muted">
						<i class="fas fa-calendar me-1"></i>
						{{ $post->created_at->format('d.m.Y') }}
					</small>
				</div>
			</div>
			
			<!-- Дополнительная информация -->
			<div class="row g-2 mt-2">
				<!-- Просмотры -->
				<div class="col-4">
					<small class="text-muted">
						<i class="fas fa-eye me-1"></i>
						{{ $post->views }}
					</small>
				</div>
				
				<!-- Количество фото -->
				<div class="col-4 text-center">
					<small class="text-muted">
						<i class="fas fa-images me-1"></i>
						{{ $post->photos ? $post->photos->count() : 0 }}
					</small>
				</div>
				
				<!-- Комментарии -->
				<div class="col-4 text-end">
					<small class="text-muted">
						<i class="fas fa-comments me-1"></i>
						{{ $post->comments ? $post->comments->count() : 0 }}
					</small>
				</div>
			</div>
		</div>
	</div>

	<!-- Футер карточки (опционально) -->
	@if(isset($showFooter) && $showFooter)
		<div class="card-footer bg-transparent border-0 pt-0">
			<div class="d-flex justify-content-between align-items-center">
				<!-- Рейтинг звездами -->
				@if($post->rating > 0)
					<div class="rating-stars">
						@for($i = 1; $i <= 5; $i++)
							@if($i <= floor($post->rating))
								<i class="fas fa-star text-warning"></i>
							@elseif($i - 0.5 <= $post->rating)
								<i class="fas fa-star-half-alt text-warning"></i>
							@else
								<i class="far fa-star text-muted"></i>
							@endif
						@endfor
						<small class="ms-1 text-muted">({{ $post->rating }})</small>
					</div>
				@else
					<small class="text-muted">Без рейтинга</small>
				@endif
				
				<!-- Кнопка "Читать далее" -->
				<a href="{{ route('posts.show', $post->slug) }}" 
				   class="btn btn-outline-primary btn-sm">
					Читать далее <i class="fas fa-arrow-right ms-1"></i>
				</a>
			</div>
		</div>
	@endif
</div>

<!-- Стили для карточки поста -->
<style>
	.post-card {
		transition: all 0.3s ease;
		border: none;
		border-radius: 12px;
		overflow: hidden;
	}
	
	.post-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
	}
	
	.post-card .card-img-container {
		overflow: hidden;
	}
	
	.post-card .card-img-top {
		transition: transform 0.3s ease;
	}
	
	.post-card:hover .card-img-top {
		transform: scale(1.05);
	}
	
	.post-card .card-title a {
		transition: color 0.3s ease;
	}
	
	.post-card:hover .card-title a {
		color: var(--bs-primary) !important;
	}
	
	.post-card .badge {
		font-size: 0.75rem;
		backdrop-filter: blur(10px);
		background-color: rgba(13, 110, 253, 0.9) !important;
	}
	
	.post-card .badge.bg-warning {
		background-color: rgba(255, 193, 7, 0.9) !important;
	}
	
	.post-card .rating-stars i {
		font-size: 0.875rem;
	}
	
	/* Адаптивность */
	@media (max-width: 576px) {
		.post-card .card-img-top {
			height: 180px !important;
		}
		
		.post-card .card-title {
			font-size: 1.1rem;
		}
		
		.post-card .card-text {
			font-size: 0.9rem;
		}
	}
	
	@media (min-width: 768px) and (max-width: 991.98px) {
		.post-card .card-img-top {
			height: 220px !important;
		}
	}
	
	@media (min-width: 992px) {
		.post-card .card-img-top {
			height: 200px !important;
		}
	}
</style>
