@extends('layouts.app')

@section('title', $post->title . ' — Отдых в Карелии')
@section('description', Str::limit(strip_tags($post->description), 150))

@section('content')
	<div class="container my-4">
		<div class="row g-4">
			<div class="col-lg-8">
				<div class="card mb-4">
					@php $mainPhoto = $post->getMainPhoto(); @endphp
					@if($mainPhoto)
						@php $mainPhotoIndex = $post->photos->search(function($photo) use ($mainPhoto) { return $photo->id === $mainPhoto->id; }); @endphp
						<img src="{{ asset($mainPhoto->photo_path) }}" 
							 class="card-img-top gallery-thumbnail" 
							 alt="{{ $post->title }}" 
							 style="max-height:480px;object-fit:cover;cursor:pointer" 
							 data-gallery-index="{{ $mainPhotoIndex }}"
							 data-gallery-src="{{ asset($mainPhoto->photo_path) }}"
							 data-gallery-alt="{{ $post->title }}">
					@else
						<img src="https://placehold.co/1200x480?text=Karelia" class="card-img-top" alt="{{ $post->title }}">
					@endif
					<div class="card-body">
						<div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
							<div>
								<h1 class="h3 mb-1">{{ $post->title }}</h1>
								<div class="text-muted small">
									<i class="fas fa-user me-1"></i>{{ $post->user->name }}
									<span class="mx-2">•</span>
									<i class="far fa-clock me-1"></i>{{ $post->created_at->format('d.m.Y') }}
									<span class="mx-2">•</span>
									<i class="fas fa-folder me-1"></i>
									<a href="{{ route('categories.show', $post->category->slug) }}" class="text-decoration-none">{{ $post->category->name }}</a>
								</div>
							</div>
							<div class="rating-display">
								@for($i=1; $i<=5; $i++)
									<i class="{{ $i <= round($post->rating) ? 'fas' : 'far' }} fa-star {{ $i <= round($post->rating) ? 'text-warning' : 'text-muted' }} fa-lg"></i>
								@endfor
								<small class="ms-1 text-muted align-middle">{{ number_format((float)$post->rating, 1, '.', '') }}</small>
							</div>
						</div>

						@if($post->address)
							<p class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $post->address }}</p>
						@endif

						<div class="content">
							{{ strip_tags($post->description) }}
						</div>
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
						</ul>
					</div>
				</div>

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
				line-height: 1.6;
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
