@extends('layouts.app')

@php $mainPhoto = $post->getMainPhoto(); @endphp

@section('title', $post->title . ' — Отдых в Карелии')
@section('description', Str::limit(strip_tags($post->description), 150))
@section('og:title', $post->title . ' — Отдых в Карелии')
@section('og:description', Str::limit(strip_tags($post->description), 150))
@section('og:image', $mainPhoto ? asset($mainPhoto->photo_path) : asset('images/og-image.jpg'))

@section('content')
	<!-- Hero Section -->
	<div class="post-hero">
		{{-- $mainPhoto уже вычислен выше --}}
		@if($mainPhoto)
			@php $mainPhotoIndex = $post->photos->search(function($photo) use ($mainPhoto) { return $photo->id === $mainPhoto->id; }); @endphp
			<div class="hero-image" style="background-image: url('{{ asset($mainPhoto->photo_path) }}');">
				<div class="hero-overlay">
					<div class="container">
						<div class="row">
							<div class="col-lg-8">
								<div class="hero-content">
									<div class="hero-badges">
										<span class="hero-badge cat-{{ $post->category->slug }}">
											<i class="fas fa-tag me-1"></i>{{ $post->category->name }}
										</span>
										@if($post->rating > 0)
											<span class="hero-badge rating">
												<i class="fas fa-star me-1"></i>{{ number_format($post->rating, 1) }}
											</span>
										@endif
									</div>
									<h1 class="hero-title">{{ $post->title }}</h1>
									<div class="hero-meta">
										<div class="hero-author">
											<i class="fas fa-user me-1"></i>{{ $post->user->name }}
											<span class="mx-2">•</span>
											<i class="fas fa-calendar me-1"></i>{{ $post->created_at->format('d.m.Y') }}
										</div>
										<div class="hero-stats">
										<span><i class="fas fa-eye me-1"></i>{{ $post->views }}</span>
										<span><i class="fas fa-comments me-1"></i>{{ $post->comments_count }}</span>
										<span><i class="fas fa-star me-1"></i>{{ $post->ratings_count }}</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		@else
			<div class="hero-image hero-placeholder">
				<div class="hero-overlay">
					<div class="container">
						<div class="row">
							<div class="col-lg-8">
								<div class="hero-content">
									<div class="hero-badges">
										<span class="hero-badge cat-{{ $post->category->slug }}">
											<i class="fas fa-tag me-1"></i>{{ $post->category->name }}
										</span>
									</div>
									<h1 class="hero-title">{{ $post->title }}</h1>
									<div class="hero-meta">
										<div class="hero-author">
											<i class="fas fa-user me-1"></i>{{ $post->user->name }}
											<span class="mx-2">•</span>
											<i class="fas fa-calendar me-1"></i>{{ $post->created_at->format('d.m.Y') }}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endif
	</div>

	<div class="container my-4">
		<div class="row g-4">
			<div class="col-lg-8">
				<div class="card mb-4">
					<div class="card-body">
						<!-- Описание поста -->

						@if($post->address)
							<div class="post-location mb-4">
								<i class="fas fa-map-marker-alt text-primary me-2"></i>
								<span class="fw-medium">{{ $post->address }}</span>
							</div>
						@endif

						<div class="post-content">
							{!! $post->description !!}
						</div>

						@if($post->tags->isNotEmpty())
							<div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
								<span class="text-muted small"><i class="fas fa-tags me-1"></i>Теги:</span>
								@foreach($post->tags as $tag)
									<a href="{{ route('tags.show', $tag->slug) }}"
									   class="badge text-decoration-none"
									   style="background: var(--bs-primary-bg-subtle, #cfe2ff); color: var(--bs-primary-text-emphasis, #084298); font-weight: 500; font-size: 0.8rem; padding: 5px 10px; border-radius: 20px;">
										<i class="fas fa-tag me-1" style="font-size: 0.7rem;"></i>{{ $tag->name }}
									</a>
								@endforeach
							</div>
						@endif

						@if($post->website_url)
							<div class="mt-3">
								<a href="{{ $post->website_url }}" target="_blank" rel="noopener noreferrer" 
								   class="btn btn-outline-primary">
									<i class="fas fa-external-link-alt me-2"></i>Официальный сайт
								</a>
							</div>
						@endif
					</div>
				</div>

				@if($post->photos->count() > 0)
					<div class="card mb-4">
						<div class="card-body">
							<h5 class="mb-3"><i class="far fa-images me-2"></i>Галерея</h5>
							<div class="row g-2">
								@foreach($post->photos as $index => $photo)
									<div class="col-4 col-md-3 col-lg-2">
										<img src="{{ asset($photo->photo_path) }}" 
											 alt="Фото {{ $index + 1 }}" 
											 class="img-fluid rounded gallery-thumbnail" 
											 style="cursor:pointer; height: 80px; object-fit: cover; width: 100%;" 
											 data-gallery-index="{{ $index }}"
											 data-gallery-src="{{ asset($photo->photo_path) }}"
											 data-gallery-alt="Фото {{ $index + 1 }}">
									</div>
								@endforeach
							</div>
							
							@if($post->is_personal_photos)
								<div class="mt-3 text-muted small">
									<i class="fas fa-camera me-1"></i>Фотографии автора поста
								</div>
							@elseif($post->photo_source)
								<div class="mt-3">
									<small class="text-muted">Источник фотографий: </small>
									<a href="{{ $post->photo_source }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
										{{ $post->photo_source }}
										<i class="fas fa-external-link-alt ms-1"></i>
									</a>
								</div>
							@endif
						</div>
					</div>
				@endif

				<!-- Компонент карты поста -->
				<x-post-map :post="$post" />

				<!-- Компонент рейтинга -->
				<x-rating :post="$post" :canVote="auth()->check() && auth()->id() !== $post->user_id" :userRating="auth()->check() ? $post->ratings()->where('user_id', auth()->id())->first()?->value : null" />

				<!-- Компонент комментариев -->
				<x-comments :post="$post" />
			</div>

			<div class="col-lg-4">
				<div class="card mb-4">
					<div class="card-body">
						<h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Информация</h5>
						<ul class="list-unstyled mb-0">
							@if($post->address)
								<li class="mb-2"><i class="fas fa-map-pin text-primary me-2"></i>{{ $post->address }}</li>
							@endif
							<li class="mb-2"><i class="fas fa-folder text-primary me-2"></i><a href="{{ route('categories.show', $post->category->slug) }}" class="text-decoration-none">{{ $post->category->name }}</a></li>
							<li class="mb-2"><i class="fas fa-user text-primary me-2"></i>{{ $post->user->name }}</li>
							<li class="mb-2"><i class="far fa-clock text-primary me-2"></i>{{ $post->created_at->format('d.m.Y H:i') }}</li>

							@if($post->latitude && $post->longitude)
								<li class="pt-2 mt-1 border-top">
									<div class="dropdown">
										<button class="btn btn-outline-primary btn-sm w-100 dropdown-toggle"
												type="button"
												data-bs-toggle="dropdown"
												aria-expanded="false">
											<i class="fas fa-route me-2"></i>Проложить маршрут
										</button>
										<ul class="dropdown-menu w-100">
											<li>
												<a class="dropdown-item" href="#"
												   onclick="openNavApp('yandexnavi://build_route_on_map?lat_to={{ $post->latitude }}&lon_to={{ $post->longitude }}&zoom=14','https://yandex.ru/maps/?rtext=~{{ $post->latitude }},{{ $post->longitude }}&rtt=auto'); return false;">
													<i class="fas fa-car me-2 text-danger"></i>Яндекс.Навигатор
												</a>
											</li>
											<li>
												<a class="dropdown-item"
												   href="https://yandex.ru/maps/?rtext=~{{ $post->latitude }},{{ $post->longitude }}&rtt=auto"
												   target="_blank" rel="noopener noreferrer">
													<i class="fas fa-map-marked-alt me-2 text-warning"></i>Яндекс.Карты
												</a>
											</li>
											<li><hr class="dropdown-divider"></li>
											<li>
												<a class="dropdown-item"
												   href="https://www.google.com/maps/dir/?api=1&destination={{ $post->latitude }},{{ $post->longitude }}"
												   target="_blank" rel="noopener noreferrer">
													<i class="fab fa-google me-2 text-primary"></i>Google Maps
												</a>
											</li>
											<li>
												<a class="dropdown-item"
												   href="https://2gis.ru/routeTo?point={{ $post->longitude }},{{ $post->latitude }}&m=walk"
												   target="_blank" rel="noopener noreferrer">
													<i class="fas fa-map-pin me-2 text-success"></i>2ГИС
												</a>
											</li>
										</ul>
									</div>
								</li>
							@endif
						</ul>
					</div>
				</div>

			{{-- Рядом: ST_Distance_Sphere (MySQL 8 / MariaDB), до 2 постов на категорию, 50 км --}}
			@if($showNearbyBlock)
				<div class="card mb-4 border-info">
					<div class="card-body">
						<h5 class="mb-3">
							<i class="fas fa-location-arrow text-info me-2"></i>Что находится рядом с «{{ $post->title }}»
						</h5>
						@if($nearbyAttractions->isNotEmpty())
							<h6 class="text-secondary small text-uppercase mb-2">Достопримечательности</h6>
							<div class="d-flex flex-column gap-3 mb-4">
								@foreach($nearbyAttractions as $near)
									<div class="d-flex gap-2 align-items-start">
										@if($near->mainPhoto)
											<img src="{{ asset($near->mainPhoto->photo_path) }}"
												 alt="{{ $near->title }}"
												 class="rounded flex-shrink-0"
												 style="width: 64px; height: 64px; object-fit: cover;">
										@else
											<div class="rounded flex-shrink-0 bg-light d-flex align-items-center justify-content-center"
												 style="width: 64px; height: 64px;">
												<i class="fas fa-image text-muted"></i>
											</div>
										@endif
										<div class="overflow-hidden flex-grow-1">
											<a href="{{ route('posts.show', $near->slug) }}"
											   class="d-block text-decoration-none text-dark fw-medium lh-sm mb-1"
											   style="font-size: 0.875rem;">
												{{ Str::limit($near->title, 60) }}
											</a>
											<small class="text-muted">
												<i class="fas fa-route me-1" style="font-size: 0.7rem;"></i>
												{{ number_format((float) $near->distance_meters / 1000, 1, ',', ' ') }} км
											</small>
										</div>
									</div>
								@endforeach
							</div>
							<div class="mb-3">
								<a href="{{ route('posts.index', ['category' => \App\Services\NearbyPostsService::SLUG_ATTRACTIONS]) }}"
								   class="btn btn-outline-info btn-sm w-100">
									<i class="fas fa-list me-1"></i>Все достопримечательности
								</a>
							</div>
						@endif
						@if($nearbyRestPlaces->isNotEmpty())
							<h6 class="text-secondary small text-uppercase mb-2">Места отдыха</h6>
							<div class="d-flex flex-column gap-3 mb-3">
								@foreach($nearbyRestPlaces as $near)
									<div class="d-flex gap-2 align-items-start">
										@if($near->mainPhoto)
											<img src="{{ asset($near->mainPhoto->photo_path) }}"
												 alt="{{ $near->title }}"
												 class="rounded flex-shrink-0"
												 style="width: 64px; height: 64px; object-fit: cover;">
										@else
											<div class="rounded flex-shrink-0 bg-light d-flex align-items-center justify-content-center"
												 style="width: 64px; height: 64px;">
												<i class="fas fa-image text-muted"></i>
											</div>
										@endif
										<div class="overflow-hidden flex-grow-1">
											<a href="{{ route('posts.show', $near->slug) }}"
											   class="d-block text-decoration-none text-dark fw-medium lh-sm mb-1"
											   style="font-size: 0.875rem;">
												{{ Str::limit($near->title, 60) }}
											</a>
											<small class="text-muted">
												<i class="fas fa-route me-1" style="font-size: 0.7rem;"></i>
												{{ number_format((float) $near->distance_meters / 1000, 1, ',', ' ') }} км
											</small>
										</div>
									</div>
								@endforeach
							</div>
							<a href="{{ route('posts.index', ['category' => \App\Services\NearbyPostsService::SLUG_REST]) }}"
							   class="btn btn-outline-info btn-sm w-100">
								<i class="fas fa-list me-1"></i>Все места отдыха
							</a>
						@endif
					</div>
				</div>
			@endif

			{{-- Похожие посты --}}
			@if($relatedPosts->isNotEmpty())
				<div class="card mb-4">
					<div class="card-body">
						<h5 class="mb-3">
							<i class="fas fa-compass text-primary me-2"></i>Похожие места
						</h5>
						<div class="d-flex flex-column gap-3">
							@foreach($relatedPosts as $related)
								<div class="d-flex gap-2 align-items-start">
									@if($related->mainPhoto)
										<img src="{{ asset($related->mainPhoto->photo_path) }}"
											 alt="{{ $related->title }}"
											 class="rounded flex-shrink-0"
											 style="width: 64px; height: 64px; object-fit: cover;">
									@else
										<div class="rounded flex-shrink-0 bg-light d-flex align-items-center justify-content-center"
											 style="width: 64px; height: 64px;">
											<i class="fas fa-image text-muted"></i>
										</div>
									@endif
									<div class="overflow-hidden">
										<a href="{{ route('posts.show', $related->slug) }}"
										   class="d-block text-decoration-none text-dark fw-medium lh-sm mb-1"
										   style="font-size: 0.875rem;">
											{{ Str::limit($related->title, 50) }}
										</a>
										@if($related->rating > 0)
											<small class="text-muted">
												<i class="fas fa-star text-warning me-1" style="font-size: 0.7rem;"></i>
												{{ number_format($related->rating, 1) }}
											</small>
										@endif
									</div>
								</div>
							@endforeach
						</div>
						<div class="mt-3">
							<a href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
							   class="btn btn-outline-primary btn-sm w-100">
								<i class="fas fa-list me-1"></i>Cмотреть все «{{ $post->category->name }}»
							</a>
						</div>
					</div>
				</div>
			@endif

			@can('update', $post)
				<div class="d-flex gap-2 mb-4">
						<a href="{{ route('posts.edit', $post->slug) }}" class="btn btn-outline-secondary"><i class="fas fa-edit me-2"></i>Редактировать</a>
						<form method="POST" action="{{ route('posts.update', $post->slug) }}" class="d-none" id="post-update-form">@csrf @method('PUT')</form>
						@can('delete', $post)
							<form method="POST" action="{{ route('posts.destroy', $post->slug) }}">
								@csrf
								@method('DELETE')
								<button type="submit" class="btn btn-outline-danger" data-confirm="Удалить пост? Это действие необратимо."><i class="fas fa-trash me-2"></i>Удалить</button>
							</form>
						@endcan
					</div>
				@endcan
			</div>
		</div>
	</div>

	<!-- Advanced Gallery Modal -->
	<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content bg-dark">
				<!-- Header -->
				<div class="modal-header border-0">
					<h5 class="modal-title text-white" id="galleryTitle">Галерея</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				</div>
				
				<!-- Main Content -->
				<div class="modal-body p-0 position-relative">
					<!-- Navigation Arrows -->
					<button class="gallery-nav gallery-nav-prev" id="galleryPrev">
						<i class="fas fa-chevron-left"></i>
					</button>
					<button class="gallery-nav gallery-nav-next" id="galleryNext">
						<i class="fas fa-chevron-right"></i>
					</button>
					
					<!-- Main Image Container -->
					<div class="gallery-main-container" id="galleryMainContainer">
						<img id="galleryMainImage" src="" alt="" class="gallery-main-image">
					</div>
					
					<!-- Zoom Controls -->
					<div class="gallery-zoom-controls">
						<button class="gallery-zoom-btn" id="galleryZoomIn" title="Увеличить">
							<i class="fas fa-plus"></i>
						</button>
						<button class="gallery-zoom-btn" id="galleryZoomOut" title="Уменьшить">
							<i class="fas fa-minus"></i>
						</button>
						<button class="gallery-zoom-btn" id="galleryZoomReset" title="Сбросить">
							<i class="fas fa-expand-arrows-alt"></i>
						</button>
					</div>
				</div>
				
				<!-- Footer with Thumbnails and Counter -->
				<div class="modal-footer border-0 bg-dark">
					<div class="d-flex justify-content-between align-items-center w-100">
						<!-- Counter -->
						<div class="gallery-counter text-white">
							<span id="galleryCurrent">1</span> из <span id="galleryTotal">{{ $post->photos->count() }}</span>
						</div>
						
						<!-- Thumbnails -->
						<div class="gallery-thumbnails-container" id="galleryThumbnails">
							@foreach($post->photos as $index => $photo)
								<img src="{{ asset($photo->photo_path) }}" 
									 alt="Фото {{ $index + 1 }}" 
									 class="gallery-thumbnail-small {{ $index === 0 ? 'active' : '' }}"
									 data-gallery-index="{{ $index }}"
									 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer; margin: 0 2px; border: 2px solid transparent;">
							@endforeach
						</div>
						
						<!-- Close Button -->
						<button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
							<i class="fas fa-times me-1"></i>Закрыть
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	@push('styles')
		<style>
			/* Content Styles */
			.content {
				white-space: pre-line;
				word-wrap: break-word;
				line-height: 1.4;
				text-align: justify;
			}
			
			/* Gallery Styles */
			.gallery-thumbnail {
				transition: transform 0.2s ease, box-shadow 0.2s ease;
			}
			.gallery-thumbnail:hover {
				transform: scale(1.05);
				box-shadow: 0 4px 8px rgba(0,0,0,0.3);
			}
			
			/* Gallery Modal Styles */
			.gallery-main-container {
				position: relative;
				width: 100%;
				height: calc(100vh - 200px);
				display: flex;
				align-items: center;
				justify-content: center;
				overflow: hidden;
			}
			
			.gallery-main-image {
				max-width: 100%;
				max-height: 100%;
				object-fit: contain;
				transition: transform 0.3s ease;
				cursor: grab;
			}
			
			.gallery-main-image:active {
				cursor: grabbing;
			}
			
			.gallery-nav {
				position: absolute;
				top: 50%;
				transform: translateY(-50%);
				background: rgba(0,0,0,0.7);
				border: none;
				color: white;
				font-size: 24px;
				padding: 15px 20px;
				cursor: pointer;
				z-index: 1000;
				transition: background 0.2s ease;
			}
			
			.gallery-nav:hover {
				background: rgba(0,0,0,0.9);
			}
			
			.gallery-nav-prev {
				left: 20px;
			}
			
			.gallery-nav-next {
				right: 20px;
			}
			
			.gallery-zoom-controls {
				position: absolute;
				top: 20px;
				right: 20px;
				display: flex;
				gap: 10px;
				z-index: 1000;
			}
			
			.gallery-zoom-btn {
				background: rgba(0,0,0,0.7);
				border: none;
				color: white;
				width: 40px;
				height: 40px;
				border-radius: 50%;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				transition: background 0.2s ease;
			}
			
			.gallery-zoom-btn:hover {
				background: rgba(0,0,0,0.9);
			}
			
			.gallery-thumbnails-container {
				display: flex;
				gap: 5px;
				overflow-x: auto;
				padding: 10px 0;
				max-width: 400px;
			}
			
			.gallery-thumbnail-small {
				transition: border-color 0.2s ease, transform 0.2s ease;
			}
			
			.gallery-thumbnail-small:hover {
				transform: scale(1.1);
			}
			
			.gallery-thumbnail-small.active {
				border-color: #007bff !important;
			}
			
			.gallery-counter {
				font-size: 14px;
				font-weight: 500;
			}
			
			/* Mobile Responsive */
			@media (max-width: 768px) {
				.gallery-nav {
					padding: 10px 15px;
					font-size: 20px;
				}
				
				.gallery-nav-prev {
					left: 10px;
				}
				
				.gallery-nav-next {
					right: 10px;
				}
				
				.gallery-zoom-controls {
					top: 10px;
					right: 10px;
				}
				
				.gallery-zoom-btn {
					width: 35px;
					height: 35px;
				}
				
				.gallery-thumbnails-container {
					max-width: 250px;
				}
				
				.gallery-thumbnail-small {
					width: 50px !important;
					height: 50px !important;
				}
			}
		</style>
	@endpush

	@push('schema')
	@php
	$ratingsCount = $post->ratings->count();

	$postSchema = [
	    '@context'      => 'https://schema.org',
	    '@type'         => 'TouristAttraction',
	    'name'          => $post->title,
	    'description'   => html_entity_decode(
	                           strip_tags($post->description),
	                           ENT_QUOTES | ENT_HTML5,
	                           'UTF-8'
	                       ),
	    'url'           => route('posts.show', $post->slug),
	    'inLanguage'    => 'ru-RU',
	    'datePublished' => $post->created_at->toIso8601String(),
	    'dateModified'  => $post->updated_at->toIso8601String(),
	    'author'        => [
	        '@type' => 'Person',
	        'name'  => $post->user->name,
	    ],
	    'isPartOf' => [
	        '@type' => 'WebSite',
	        'name'  => 'Отдых в Карелии',
	        'url'   => url('/'),
	    ],
	];

	if ($post->photos->isNotEmpty()) {
	    $postSchema['image'] = $post->photos
	        ->map(fn($p) => asset($p->photo_path))
	        ->values()
	        ->toArray();
	}

	if ($post->latitude && $post->longitude) {
	    $postSchema['geo'] = [
	        '@type'     => 'GeoCoordinates',
	        'latitude'  => (float) $post->latitude,
	        'longitude' => (float) $post->longitude,
	    ];
	}

	if ($post->address) {
	    $postSchema['address'] = [
	        '@type'           => 'PostalAddress',
	        'streetAddress'   => $post->address,
	        'addressRegion'   => 'Республика Карелия',
	        'addressCountry'  => 'RU',
	    ];
	}

	if ($ratingsCount > 0) {
	    $postSchema['aggregateRating'] = [
	        '@type'       => 'AggregateRating',
	        'ratingValue' => round((float) $post->rating, 1),
	        'reviewCount' => $ratingsCount,
	        'bestRating'  => 5,
	        'worstRating' => 1,
	    ];
	}

	if ($post->website_url) {
	    $postSchema['sameAs'] = $post->website_url;
	}

	$breadcrumbSchema = [
	    '@context' => 'https://schema.org',
	    '@type'    => 'BreadcrumbList',
	    'itemListElement' => [
	        [
	            '@type'    => 'ListItem',
	            'position' => 1,
	            'name'     => 'Главная',
	            'item'     => route('home'),
	        ],
	        [
	            '@type'    => 'ListItem',
	            'position' => 2,
	            'name'     => $post->category->name,
	            'item'     => route('categories.show', $post->category->slug),
	        ],
	        [
	            '@type'    => 'ListItem',
	            'position' => 3,
	            'name'     => $post->title,
	            'item'     => route('posts.show', $post->slug),
	        ],
	    ],
	];
	@endphp
	<script type="application/ld+json">{!! json_encode($postSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
	<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
	@endpush

	@push('scripts')
		<script>
			// Advanced Gallery System
			class PhotoGallery {
				constructor() {
					this.currentIndex = 0;
					this.images = [];
					this.zoomLevel = 1;
					this.isDragging = false;
					this.dragStart = { x: 0, y: 0 };
					this.imageOffset = { x: 0, y: 0 };
					
					this.init();
				}
				
				init() {
					// Collect all gallery images
					this.images = Array.from(document.querySelectorAll('.gallery-thumbnail')).map(img => ({
						src: img.dataset.gallerySrc,
						alt: img.dataset.galleryAlt,
						index: parseInt(img.dataset.galleryIndex)
					}));
					
					// Bind events
					this.bindEvents();
				}
				
				bindEvents() {
					// Gallery thumbnail clicks
					document.querySelectorAll('.gallery-thumbnail').forEach(thumb => {
						thumb.addEventListener('click', (e) => {
							const index = parseInt(e.target.dataset.galleryIndex);
							this.openGallery(index);
						});
					});
					
					// Modal events
					const modal = document.getElementById('galleryModal');
					modal.addEventListener('show.bs.modal', () => {
						this.resetZoom();
					});
					
					modal.addEventListener('hidden.bs.modal', () => {
						this.resetZoom();
					});
					
					// Navigation
					document.getElementById('galleryPrev').addEventListener('click', () => this.prevImage());
					document.getElementById('galleryNext').addEventListener('click', () => this.nextImage());
					
					// Zoom controls
					document.getElementById('galleryZoomIn').addEventListener('click', () => this.zoomIn());
					document.getElementById('galleryZoomOut').addEventListener('click', () => this.zoomOut());
					document.getElementById('galleryZoomReset').addEventListener('click', () => this.resetZoom());
					
					// Thumbnail navigation
					document.querySelectorAll('.gallery-thumbnail-small').forEach(thumb => {
						thumb.addEventListener('click', (e) => {
							const index = parseInt(e.target.dataset.galleryIndex);
							this.goToImage(index);
						});
					});
					
					// Keyboard navigation
					document.addEventListener('keydown', (e) => {
						if (!modal.classList.contains('show')) return;
						
						switch(e.key) {
							case 'ArrowLeft':
								e.preventDefault();
								this.prevImage();
								break;
							case 'ArrowRight':
								e.preventDefault();
								this.nextImage();
								break;
							case 'Escape':
								e.preventDefault();
								this.closeGallery();
								break;
							case '+':
							case '=':
								e.preventDefault();
								this.zoomIn();
								break;
							case '-':
								e.preventDefault();
								this.zoomOut();
								break;
							case '0':
								e.preventDefault();
								this.resetZoom();
								break;
						}
					});
					
					// Touch/swipe support
					this.bindTouchEvents();
				}
				
				bindTouchEvents() {
					const container = document.getElementById('galleryMainContainer');
					let startX = 0;
					let startY = 0;
					
					container.addEventListener('touchstart', (e) => {
						startX = e.touches[0].clientX;
						startY = e.touches[0].clientY;
					});
					
					container.addEventListener('touchend', (e) => {
						if (!startX || !startY) return;
						
						const endX = e.changedTouches[0].clientX;
						const endY = e.changedTouches[0].clientY;
						
						const diffX = startX - endX;
						const diffY = startY - endY;
						
						// Horizontal swipe
						if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
							if (diffX > 0) {
								this.nextImage();
							} else {
								this.prevImage();
							}
						}
						
						startX = 0;
						startY = 0;
					});
				}
				
				openGallery(index) {
					this.currentIndex = index;
					this.updateImage();
					this.updateCounter();
					this.updateThumbnails();
					
					// Show modal
					const modal = new bootstrap.Modal(document.getElementById('galleryModal'));
					modal.show();
				}
				
				closeGallery() {
					const modal = bootstrap.Modal.getInstance(document.getElementById('galleryModal'));
					if (modal) modal.hide();
				}
				
				prevImage() {
					this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
					this.updateImage();
					this.updateCounter();
					this.updateThumbnails();
				}
				
				nextImage() {
					this.currentIndex = (this.currentIndex + 1) % this.images.length;
					this.updateImage();
					this.updateCounter();
					this.updateThumbnails();
				}
				
				goToImage(index) {
					this.currentIndex = index;
					this.updateImage();
					this.updateCounter();
					this.updateThumbnails();
				}
				
				updateImage() {
					const img = document.getElementById('galleryMainImage');
					const currentImage = this.images[this.currentIndex];
					
					img.src = currentImage.src;
					img.alt = currentImage.alt;
					
					// Reset zoom when changing image
					this.resetZoom();
				}
				
				updateCounter() {
					document.getElementById('galleryCurrent').textContent = this.currentIndex + 1;
				}
				
				updateThumbnails() {
					document.querySelectorAll('.gallery-thumbnail-small').forEach((thumb, index) => {
						thumb.classList.toggle('active', index === this.currentIndex);
					});
				}
				
				zoomIn() {
					this.zoomLevel = Math.min(this.zoomLevel * 1.2, 5);
					this.applyZoom();
				}
				
				zoomOut() {
					this.zoomLevel = Math.max(this.zoomLevel / 1.2, 0.5);
					this.applyZoom();
				}
				
				resetZoom() {
					this.zoomLevel = 1;
					this.imageOffset = { x: 0, y: 0 };
					this.applyZoom();
				}
				
				applyZoom() {
					const img = document.getElementById('galleryMainImage');
					img.style.transform = `scale(${this.zoomLevel}) translate(${this.imageOffset.x}px, ${this.imageOffset.y}px)`;
				}
			}
			
			// Initialize gallery when DOM is ready
			document.addEventListener('DOMContentLoaded', function() {
				new PhotoGallery();
			});

		</script>
	@endpush
@endsection
