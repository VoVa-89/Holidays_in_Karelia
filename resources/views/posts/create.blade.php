@extends('layouts.app')

@section('title', 'Создать пост — Отдых в Карелии')
@section('description', 'Добавьте новое место: название, описание, категория, адрес, фото и координаты на карте.')

@section('content')
	<div class="container my-4">
		<h1 class="h3 mb-3"><i class="fas fa-plus text-primary me-2"></i>Создание поста</h1>


		<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="post-create-form">
			@csrf
			<input type="hidden" name="status" id="status" value="moderation">
					<input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
					<input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
					<input type="hidden" name="main_index" id="main_index" value="{{ old('main_index', 0) }}">

			<div class="row g-4">
				<div class="col-lg-8">
					<div class="card mb-4">
						<div class="card-body">
							<div class="mb-3">
								<label for="title" class="form-label">Название</label>
								<input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Например: Остров Кижи">
								@error('title')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="description" class="form-label">Описание</label>
								<textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="8">{{ old('description') }}</textarea>
								@error('description')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="row g-3">
								<div class="col-md-6">
									<label for="category_id" class="form-label">Категория</label>
									<select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
										<option value="" disabled selected>Выберите категорию</option>
										@foreach($categories as $category)
											<option value="{{ $category->id }}" {{ (int)old('category_id') === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
										@endforeach
									</select>
									@error('category_id')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
								<div class="col-md-6">
									<label for="address" class="form-label">Адрес</label>
									<div class="input-group">
										<input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Начните вводить адрес...">
										<button type="button" id="geocode-btn" class="btn btn-outline-primary" title="Определить координаты по адресу">
											<i class="fas fa-map-marker-alt"></i>
										</button>
									</div>
									<div class="form-text">Введите адрес и нажмите кнопку для определения координат, либо отметьте на карте.</div>
									@error('address')
										<div class="invalid-feedback d-block">{{ $message }}</div>
									@enderror
									@error('latitude')
										<div class="invalid-feedback d-block">{{ $message }}</div>
									@enderror
									@error('longitude')
										<div class="invalid-feedback d-block">{{ $message }}</div>
									@enderror
									<div id="geocode-status" class="small mt-1"></div>
								</div>
							</div>
						</div>
					</div>

					<div class="card mb-4">
						<div class="card-body">
							<h5 class="mb-3"><i class="far fa-images me-2"></i>Фотографии</h5>
							<input type="file" name="photos[]" id="photos" class="form-control" accept="image/*" multiple>
							<div class="form-text">Выберите несколько изображений. Отметьте основную фотографию.</div>
							<div id="photos-preview" class="row g-2 mt-2"></div>
						</div>
					</div>

					<div class="card mb-4">
						<div class="card-body">
							<h5 class="mb-3"><i class="fas fa-map-marked-alt me-2"></i>Карта</h5>
							
							<!-- Loader для карты -->
							<div id="create-map-loader" class="text-center py-4">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Загрузка карты...</span>
								</div>
								<p class="mt-2 text-muted">Загрузка карты...</p>
							</div>
							
							<!-- Контейнер для карты -->
							<div id="create-map" style="width:100%;height:360px;border-radius:12px;overflow:hidden;display:none;"></div>
							
							<!-- Сообщение об ошибке загрузки карты -->
							<div id="create-map-error" class="alert alert-warning" style="display:none;">
								<i class="fas fa-exclamation-triangle me-2"></i>
								<strong>Ошибка загрузки карты</strong><br>
								<small>Не удалось загрузить интерактивную карту. Вы можете указать координаты вручную или попробовать позже.</small>
							</div>
							
							<div class="small text-muted mt-2">Перетащите метку для уточнения координат.</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="card mb-4">
						<div class="card-body">
							<h5 class="mb-3"><i class="fas fa-paper-plane me-2"></i>Публикация</h5>
							<p class="text-muted mb-3">Пост будет отправлен на модерацию перед публикацией.</p>
							<button type="submit" class="btn btn-primary w-100" id="submit-btn"><i class="fas fa-paper-plane me-2"></i>Отправить на модерацию</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>

	@push('styles')
		<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
		<style>
			.photo-preview-card {
				transition: transform 0.2s ease, box-shadow 0.2s ease;
			}
			.photo-preview-card:hover {
				transform: translateY(-2px);
				box-shadow: 0 4px 8px rgba(0,0,0,0.15);
			}
			.photo-preview-card .card-img-top {
				border-radius: 0.375rem 0.375rem 0 0;
			}
			.drag-over {
				background-color: #e3f2fd !important;
				border: 2px dashed #2196f3 !important;
				border-radius: 0.5rem;
			}
			#photos-preview {
				min-height: 100px;
				border: 2px dashed #dee2e6;
				border-radius: 0.5rem;
				padding: 1rem;
				transition: border-color 0.2s ease;
			}
			#photos-preview:empty::before {
				content: "Перетащите изображения сюда или выберите файлы";
				display: block;
				text-align: center;
				color: #6c757d;
				font-style: italic;
				padding: 2rem;
			}
			
		</style>
	@endpush

	@push('scripts')
		<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
		<script src="{{ asset('js/photo-preview.js') }}"></script>
		<script>
			// WYSIWYG (Quill) -> hidden textarea binding
			(function(){
				const el = document.getElementById('description');
				const initial = el.value;
				const wrapper = document.createElement('div');
				wrapper.id = 'editor';
				wrapper.className = 'form-control';
				wrapper.style.minHeight = '240px';
				el.parentNode.insertBefore(wrapper, el);
				el.style.display = 'none';
				const quill = new Quill('#editor', { theme: 'snow', placeholder: 'Опишите место, как добраться, что посмотреть...' });
				quill.root.innerHTML = initial ? initial : '';
				document.getElementById('post-create-form').addEventListener('submit', function(){
					el.value = quill.root.innerHTML;
				});
			})();

			// Yandex Maps: geocode by address + draggable marker -> lat/lng
			let createMap;
			let createMapLoadTimeout;
			let marker; // делаем доступным вне init

			// Функция показа ошибки загрузки карты
			function showCreateMapError() {
				document.getElementById('create-map-loader').style.display = 'none';
				document.getElementById('create-map-error').style.display = 'block';
			}

			// Функция скрытия лоадера и показа карты
			function showCreateMap() {
				document.getElementById('create-map-loader').style.display = 'none';
				document.getElementById('create-map').style.display = 'block';
			}

			function initCreateMap() {
				// Устанавливаем таймаут для загрузки карты (10 секунд)
				createMapLoadTimeout = setTimeout(function() {
					showCreateMapError();
				}, 10000);

				// Проверяем, загружен ли API Яндекс.Карт
				if (typeof ymaps === 'undefined') {
					console.error('API Яндекс.Карт не загружен');
					showCreateMapError();
					return;
				}

				try {
					ymaps.ready(function(){
						try {
							createMap = new ymaps.Map('create-map', { 
								center: [61.787, 34.364], 
								zoom: 7, 
								controls: ['zoomControl','fullscreenControl', 'typeSelector'] 
							});
							
							// Обработчик успешной загрузки карты
							createMap.events.add('ready', function() {
								clearTimeout(createMapLoadTimeout);
								showCreateMap();
							});
							
							// создаём маркер и сохраняем в внешнюю переменную
							marker = new ymaps.Placemark(createMap.getCenter(), {}, { 
								draggable: true, 
								preset: 'islands#redIcon' 
							});
							createMap.geoObjects.add(marker);

							// обработчик перетаскивания маркера
							marker.events.add('dragend', function(){ 
								setLatLng(marker.geometry.getCoordinates()); 
							});

							// принудительный показ как на других страницах
							setTimeout(function(){
								if (createMap) {
									clearTimeout(createMapLoadTimeout);
									showCreateMap();
								}
							}, 3000);
						} catch (error) {
							console.error('Ошибка создания карты:', error);
							clearTimeout(createMapLoadTimeout);
							showCreateMapError();
						}
					});
				} catch (error) {
					console.error('Ошибка инициализации ymaps:', error);
					clearTimeout(createMapLoadTimeout);
					showCreateMapError();
				}
			}

			function setLatLng(coords){
				document.getElementById('latitude').value = coords[0].toFixed(8);
				document.getElementById('longitude').value = coords[1].toFixed(8);
			}

			function updateMapCenter(coords) {
				if (createMap) {
					createMap.setCenter(coords, 14);
					if (marker) {
						marker.geometry.setCoordinates(coords);
					}
					setLatLng(coords);
				}
			}

			// Функция геокодирования адреса
			function geocodeAddress(address, callback) {
				if (!address || address.trim() === '') {
					showGeocodeStatus('Введите адрес для поиска', 'warning');
					return;
				}

				showGeocodeStatus('Поиск координат...', 'info');
				
				ymaps.geocode(address, { 
					results: 1,
					kind: 'house'
				}).then(function(res) {
					var first = res.geoObjects.get(0);
					if (!first) {
						showGeocodeStatus('Адрес не найден. Попробуйте уточнить адрес.', 'danger');
						return;
					}
					
					var coords = first.geometry.getCoordinates();
					var foundAddress = first.getAddressLine();
					
					// Обновляем поле адреса найденным значением
					document.getElementById('address').value = foundAddress;
					
					// Обновляем карту
					updateMapCenter(coords);
					
					showGeocodeStatus(`Найдено: ${foundAddress}`, 'success');
					
					if (callback) callback(coords, foundAddress);
				}).catch(function(error) {
					console.error('Ошибка геокодирования:', error);
					showGeocodeStatus('Ошибка при поиске адреса', 'danger');
				});
			}

			// Функция показа статуса геокодирования
			function showGeocodeStatus(message, type) {
				const statusEl = document.getElementById('geocode-status');
				statusEl.innerHTML = `<span class="text-${type}">${message}</span>`;
				
				// Автоматически скрываем сообщение через 5 секунд
				setTimeout(() => {
					statusEl.innerHTML = '';
				}, 5000);
			}

			// Обработчик кнопки геокодирования
			document.getElementById('geocode-btn').addEventListener('click', function() {
				const address = document.getElementById('address').value.trim();
				geocodeAddress(address);
			});

			// Обработчик Enter в поле адреса
			document.getElementById('address').addEventListener('keypress', function(e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					geocodeAddress(this.value.trim());
				}
			});

			// Автоматическое геокодирование при изменении адреса (с задержкой)
			let geocodeTimeout;
			document.getElementById('address').addEventListener('input', function() {
				clearTimeout(geocodeTimeout);
				geocodeTimeout = setTimeout(() => {
					if (this.value.trim().length > 10) { // Минимум 10 символов для автопоиска
						geocodeAddress(this.value.trim());
					}
				}, 1000); // Задержка 1 секунда
			});

			// Загружаем API Яндекс.Карт и инициализируем карту
			loadYandexMapsAPI();

			// Обработчик отправки формы - переносим файлы из PhotoPreview в форму
			document.getElementById('post-create-form').addEventListener('submit', function(e) {
				// Проверяем, есть ли файлы в PhotoPreview
				if (window.photoPreview && window.photoPreview.getFiles().length > 0) {
					// Добавляем файлы из PhotoPreview в скрытый input
					const fileInput = this.querySelector('input[name="photos[]"]');
					if (fileInput) {
						// Создаем новый DataTransfer для добавления файлов
						const dataTransfer = new DataTransfer();
						window.photoPreview.getFiles().forEach(file => {
							dataTransfer.items.add(file);
						});
						fileInput.files = dataTransfer.files;
					}
					
					// Обновляем main_index если есть главная фотография
					const mainIndexInput = this.querySelector('input[name="main_index"]');
					if (mainIndexInput && window.photoPreview.getMainIndex() !== null) {
						mainIndexInput.value = window.photoPreview.getMainIndex();
					}
					
					// Показываем индикатор загрузки
					const submitBtn = this.querySelector('button[type="submit"]');
					const originalText = submitBtn.innerHTML;
					submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Отправка...';
					submitBtn.disabled = true;
					
					// Не предотвращаем отправку формы - пусть она отправится обычным способом
					console.log('Файлы добавлены в форму, отправляем обычным способом');
				} else {
					// Если нет файлов в PhotoPreview, отправляем форму обычным способом
					console.log('Отправка формы без файлов из PhotoPreview');
				}
			});

			// Функция загрузки API Яндекс.Карт
			function loadYandexMapsAPI() {
				// Используем глобальную функцию если доступна
				if (typeof window.loadYandexMaps === 'function') {
					console.log('📡 Используем глобальную функцию загрузки API');
					window.loadYandexMaps(function() {
						ymaps.ready(function() {
							initCreateMap();
						});
					});
					return;
				}
				
				// Fallback - прямая загрузка
				if (typeof ymaps !== 'undefined') {
					console.log('API Яндекс.Карт уже загружен');
					ymaps.ready(function() {
						initCreateMap();
					});
					return;
				}

				console.log('Загружаем API Яндекс.Карт...');
				
				const script = document.createElement('script');
				const apiKey = window.yandexMapsKey || '{{ config("services.yandex.maps_key") }}';
				
				if (apiKey) {
					script.src = `https://api-maps.yandex.ru/2.1/?apikey=${apiKey}&lang=ru_RU`;
				} else {
					script.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
					console.warn('⚠️ API ключ не найден для страницы создания поста');
				}
				
				script.onload = function() {
					console.log('✅ API Яндекс.Карт загружен успешно');
					ymaps.ready(function() {
						initCreateMap();
					});
				};
				
				script.onerror = function() {
					console.error('❌ Ошибка загрузки API Яндекс.Карт');
					showCreateMapError();
				};
				
				document.head.appendChild(script);
			}

			// Photos preview + select main
			(function(){
				const input = document.getElementById('photos');
				const preview = document.getElementById('photos-preview');
				const mainIndex = document.getElementById('main_index');

				if (input && preview && mainIndex) {
				// Инициализируем PhotoPreview
				window.photoPreview = new PhotoPreview({
					input: input,
					preview: preview,
					mainIndexInput: mainIndex,
					maxFiles: 10,
					maxFileSize: 5 * 1024 * 1024, // 5MB
					allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
				});

				// Включаем drag & drop
				window.photoPreview.enableDragDrop();
					
					console.log('📷 PhotoPreview инициализирован для создания поста');
				} else {
					console.error('📷 PhotoPreview: не найдены необходимые элементы', {
						input: !!input,
						preview: !!preview,
						mainIndex: !!mainIndex
					});
				}
			})();

		</script>
	@endpush
@endsection
