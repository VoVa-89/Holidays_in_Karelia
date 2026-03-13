@props(['title', 'posts', 'icon', 'colorClass', 'categorySlug'])

<div class="card border-0 shadow-sm h-100">

	{{-- Заголовок блока --}}
	<div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
		<h5 class="mb-0 fw-bold">
			<i class="{{ $icon }} text-{{ $colorClass }} me-2"></i>{{ $title }}
		</h5>
		<a href="{{ route('posts.index', ['category' => $categorySlug]) }}"
		   class="btn btn-sm btn-outline-{{ $colorClass }}"
		   style="border: 2px solid var(--bs-{{ $colorClass }});">
			Все <i class="fas fa-arrow-right ms-1"></i>
		</a>
	</div>

	<div class="card-body p-2">
		@if($posts->isEmpty())
			<div class="text-center text-muted py-4">
				<i class="fas fa-inbox fa-2x mb-2 d-block"></i>
				Пока нет постов
			</div>
		@else
			<div class="row g-2" style="height: 220px;">

				{{-- Большая карточка (первый пост) --}}
				<div class="col-7 h-100">
					@php $featured = $posts->first(); @endphp
					@php $featuredPhoto = $featured->mainPhoto; @endphp

					<a href="{{ route('posts.show', $featured->slug) }}"
					   class="text-decoration-none d-block position-relative rounded-3 overflow-hidden h-100">

						{{-- Фото или заглушка --}}
						@if($featuredPhoto)
							<img src="{{ asset($featuredPhoto->photo_path) }}"
								 alt="{{ $featured->title }}"
								 class="w-100 h-100"
								 style="object-fit: cover; position: absolute; inset: 0;">
						@else
							<div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center"
								 style="position: absolute; inset: 0;">
								<i class="fas fa-image fa-3x text-white opacity-50"></i>
							</div>
						@endif

						{{-- Градиентный оверлей --}}
						<div style="position: absolute; inset: 0;
									background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.1) 60%, transparent 100%);">
						</div>

						{{-- Текст поверх фото --}}
						<div class="position-absolute bottom-0 start-0 p-3 text-white w-100">
							@if($featured->rating > 0)
								<span class="badge bg-warning text-dark mb-1">
									<i class="fas fa-star me-1"></i>{{ number_format((float) $featured->rating, 1) }}
								</span>
							@endif
							<div class="fw-bold lh-sm" style="font-size: 0.9rem;">
								{{ Str::limit($featured->title, 60) }}
							</div>
						</div>
					</a>
				</div>

				{{-- Две маленькие карточки (посты 2 и 3) --}}
				<div class="col-5 h-100 d-flex flex-column gap-2">
					@foreach($posts->skip(1) as $post)
						@php $photo = $post->mainPhoto; @endphp

						<a href="{{ route('posts.show', $post->slug) }}"
						   class="text-decoration-none d-flex gap-2 rounded-3 border p-2 bg-light flex-fill overflow-hidden">

							{{-- Thumbnail --}}
							<div class="flex-shrink-0 rounded-2 overflow-hidden" style="width: 64px; height: 64px;">
								@if($photo)
									<img src="{{ asset($photo->photo_path) }}"
										 alt="{{ $post->title }}"
										 class="w-100 h-100"
										 style="object-fit: cover;">
								@else
									<div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
										<i class="fas fa-image text-white" style="font-size: 0.75rem;"></i>
									</div>
								@endif
							</div>

							{{-- Текст --}}
							<div class="overflow-hidden d-flex flex-column justify-content-center">
								<div class="text-dark fw-semibold lh-sm mb-1"
									 style="font-size: 0.8rem;
											display: -webkit-box;
											-webkit-line-clamp: 2;
											-webkit-box-orient: vertical;
											overflow: hidden;">
									{{ $post->title }}
								</div>
								@if($post->rating > 0)
									<small class="text-warning">
										<i class="fas fa-star"></i>
										{{ number_format((float) $post->rating, 1) }}
									</small>
								@else
									<small class="text-muted">Без оценки</small>
								@endif
							</div>
						</a>
					@endforeach
				</div>

			</div>
		@endif
	</div>
</div>
