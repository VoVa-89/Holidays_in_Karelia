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
										<div class="position-relative" style="margin-top: 8px;">
											<div id="map{{ $post->id }}" style="height: 200px; border-radius: 8px;"></div>
											<div id="mapLoading{{ $post->id }}" class="map-loading">
												Загрузка карты...
											</div>
										</div>
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

	@push('styles')
		<style>
			/* Стили для карты в модальном окне */
			.modal-body #map{{ $post->id ?? '' }} {
				width: 100%;
				height: 200px;
				border-radius: 8px;
				border: 1px solid #dee2e6;
				box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			}
			
			/* Стили для всех карт в модальных окнах */
			.modal-body [id^="map"] {
				width: 100%;
				height: 200px;
				border-radius: 8px;
				border: 1px solid #dee2e6;
				box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			}
			
			/* Индикатор загрузки карты */
			.map-loading {
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				background: rgba(248, 249, 250, 0.9);
				border-radius: 8px;
				color: #6c757d;
				font-size: 14px;
				z-index: 10;
			}
			
			.map-loading::before {
				content: '';
				width: 20px;
				height: 20px;
				border: 2px solid #dee2e6;
				border-top: 2px solid #007bff;
				border-radius: 50%;
				animation: spin 1s linear infinite;
				margin-right: 10px;
			}
			
			@keyframes spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
		</style>
	@endpush

	@push('scripts')
		<script>
			// Отладочная информация
			console.log('🔑 Yandex Maps API Key:', '{{ config('services.yandex.maps_key') ? 'найден' : 'не найден' }}');
			console.log('📊 Количество постов с координатами:', {{ $posts->where('latitude', '!=', null)->where('longitude', '!=', null)->count() }});
		</script>
		<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey={{ config('services.yandex.maps_key') }}"></script>
		<script>
			// Глобальные переменные для карт модальных окон
			let modalMaps = {};
			let modalMapTimeouts = {};

			// Функция показа ошибки загрузки карты
			function showModalMapError(postId) {
				const loadingIndicator = document.getElementById('mapLoading' + postId);
				if (loadingIndicator) {
					loadingIndicator.innerHTML = 'Ошибка загрузки карты';
					loadingIndicator.style.color = '#dc3545';
				}
			}

			// Функция скрытия лоадера и показа карты
			function showModalMap(postId) {
				const loadingIndicator = document.getElementById('mapLoading' + postId);
				if (loadingIndicator) {
					loadingIndicator.style.display = 'none';
				}
			}

			// Функция инициализации карты в модальном окне
			function initModalMap(postId, latitude, longitude, address, title) {
				console.log('🚀 Инициализация карты модального окна для поста ' + postId);
				
				// Проверяем, не инициализирована ли уже карта для этого поста
				if (modalMaps[postId]) {
					console.log('⚠️ Карта для поста ' + postId + ' уже существует, пропускаем инициализацию');
					return;
				}
				
				// Проверяем контейнер
				const container = document.getElementById('map' + postId);
				if (!container) {
					console.error('❌ Контейнер map' + postId + ' не найден!');
					showModalMapError(postId);
					return;
				}
				
				// Проверяем, не содержит ли контейнер уже карту
				if (container.children.length > 0) {
					console.log('⚠️ Контейнер map' + postId + ' уже содержит элементы, очищаем');
					container.innerHTML = '';
				}
				
				console.log('✅ Контейнер карты модального окна найден');
				
				// Устанавливаем таймаут для загрузки карты (10 секунд)
				modalMapTimeouts[postId] = setTimeout(function() {
					console.error('⏰ Таймаут карты модального окна ' + postId);
					showModalMapError(postId);
				}, 10000);

				// Проверяем, загружен ли API Яндекс.Карт
				if (typeof ymaps === 'undefined') {
					console.error('❌ API Яндекс.Карт не загружен для модального окна ' + postId);
					showModalMapError(postId);
					return;
				}
				
				console.log('✅ API доступен для модального окна ' + postId);

				try {
					ymaps.ready(function () {
						console.log('🗺️ ymaps.ready() для модального окна ' + postId);
						try {
							// Создаем карту с центром на координатах поста
							console.log('📍 Создаем карту модального окна с координатами: ' + latitude + ', ' + longitude);
							
							modalMaps[postId] = new ymaps.Map('map' + postId, {
								center: [parseFloat(latitude), parseFloat(longitude)],
								zoom: 14,
								controls: [
									'zoomControl', 
									'geolocationControl', 
									'fullscreenControl',
									'typeSelector'
								]
							});
							
							console.log('✅ Карта модального окна ' + postId + ' создана');

							// Создаем маркер
							const marker = new ymaps.Placemark([parseFloat(latitude), parseFloat(longitude)], {
								balloonContent: address,
								hintContent: title
							}, {
								preset: 'islands#redIcon',
								iconColor: '#ff0000'
							});
							
							modalMaps[postId].geoObjects.add(marker);
							
							// Открываем балун с информацией
							marker.balloon.open();

							// Обработчик успешной загрузки карты
							modalMaps[postId].events.add('ready', function() {
								console.log('🎉 Карта модального окна ' + postId + ' готова!');
								clearTimeout(modalMapTimeouts[postId]);
								showModalMap(postId);
							});
							
							// Принудительный показ через 3 секунды
							setTimeout(function() {
								if (modalMaps[postId]) {
									console.log('⏰ Принудительно показываем карту модального окна ' + postId);
									clearTimeout(modalMapTimeouts[postId]);
									showModalMap(postId);
								}
							}, 3000);
							
						} catch (error) {
							console.error('❌ Ошибка создания карты модального окна ' + postId + ':', error);
							clearTimeout(modalMapTimeouts[postId]);
							showModalMapError(postId);
						}
					});
				} catch (error) {
					console.error('❌ Ошибка ymaps.ready для модального окна ' + postId + ':', error);
					clearTimeout(modalMapTimeouts[postId]);
					showModalMapError(postId);
				}
			}

			// Проверка загрузки API Яндекс.Карт
			function checkYandexMapsAPI() {
				if (typeof ymaps !== 'undefined') {
					console.log('✅ Yandex Maps API загружен');
					return true;
				} else {
					console.log('⏳ Yandex Maps API еще загружается...');
					return false;
				}
			}

			// Функция инициализации карты для конкретного поста
			function initMapForPost{{ $post->id ?? '' }}(postId, latitude, longitude, address, title) {
				console.log('🚀 Инициализация карты для поста ' + postId);
				
				// Проверяем, не инициализирована ли уже карта
				if (modalMaps[postId]) {
					console.log('⚠️ Карта для поста ' + postId + ' уже инициализирована, пропускаем');
					return;
				}
				
				// Проверяем, загружен ли API
				if (!checkYandexMapsAPI()) {
					console.log('⏳ API еще не загружен, ждем...');
					// Ждем загрузки API с интервалом
					const checkInterval = setInterval(function() {
						if (checkYandexMapsAPI()) {
							clearInterval(checkInterval);
							initModalMap(postId, latitude, longitude, address, title);
						}
					}, 100);
					
					// Таймаут для проверки API
					setTimeout(function() {
						clearInterval(checkInterval);
						if (!checkYandexMapsAPI()) {
							console.error('❌ API не загрузился за 5 секунд');
							showModalMapError(postId);
						}
					}, 5000);
				} else {
					// API уже загружен, запускаем инициализацию
					initModalMap(postId, latitude, longitude, address, title);
				}
			}

			// Проверка Bootstrap
			function checkBootstrap() {
				if (typeof bootstrap !== 'undefined') {
					console.log('✅ Bootstrap загружен');
					return true;
				} else {
					console.log('⏳ Bootstrap еще загружается...');
					return false;
				}
			}

			// Инициализация карт для модальных окон
			document.addEventListener('DOMContentLoaded', function() {
				console.log('🚀 DOM загружен, инициализируем обработчики модальных окон');
				console.log('🔧 Bootstrap статус:', checkBootstrap() ? 'загружен' : 'не загружен');
				
				@foreach($posts as $post)
					@if($post->latitude && $post->longitude)
						// Карта для поста {{ $post->id }}
						const previewModal{{ $post->id }} = document.getElementById('previewModal{{ $post->id }}');
						if (previewModal{{ $post->id }}) {
							// Флаг для предотвращения двойной инициализации
							let mapInitialized{{ $post->id }} = false;
							
							// Обработчик открытия модального окна
							previewModal{{ $post->id }}.addEventListener('shown.bs.modal', function() {
								console.log('📱 Открыто модальное окно для поста {{ $post->id }}');
								
								// Проверяем, не инициализирована ли уже карта
								if (!mapInitialized{{ $post->id }}) {
									mapInitialized{{ $post->id }} = true;
									initMapForPost{{ $post->id }}({{ $post->id }}, {{ $post->latitude }}, {{ $post->longitude }}, '{{ addslashes($post->address) }}', '{{ addslashes($post->title) }}');
								} else {
									console.log('⚠️ Карта для поста {{ $post->id }} уже инициализирована');
								}
							});
							
							// Обработчик закрытия модального окна
							previewModal{{ $post->id }}.addEventListener('hidden.bs.modal', function() {
								console.log('📱 Закрыто модальное окно для поста {{ $post->id }}');
								const mapContainer = document.getElementById('map{{ $post->id }}');
								const loadingIndicator = document.getElementById('mapLoading{{ $post->id }}');
								
								// Уничтожаем карту
								if (modalMaps[{{ $post->id }}]) {
									modalMaps[{{ $post->id }}].destroy();
									delete modalMaps[{{ $post->id }}];
								}
								
								// Очищаем контейнер
								if (mapContainer) {
									mapContainer.innerHTML = '';
								}
								
								// Показываем индикатор загрузки снова
								if (loadingIndicator) {
									loadingIndicator.style.display = 'flex';
									loadingIndicator.innerHTML = 'Загрузка карты...';
									loadingIndicator.style.color = '#6c757d';
								}
								
								// Очищаем таймаут
								if (modalMapTimeouts[{{ $post->id }}]) {
									clearTimeout(modalMapTimeouts[{{ $post->id }}]);
									delete modalMapTimeouts[{{ $post->id }}];
								}
								
								// Сбрасываем флаг инициализации
								mapInitialized{{ $post->id }} = false;
							});
						}
					@endif
				@endforeach
			});
		</script>
	@endpush
@endsection
