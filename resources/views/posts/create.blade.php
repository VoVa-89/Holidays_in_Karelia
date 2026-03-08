@extends('layouts.app')

@section('title', 'Создать пост — Отдых в Карелии')
@section('description', 'Добавьте новое место: название, описание, категория, адрес, фото и координаты на карте.')

@section('content')
	<div class="container my-4">
		<div class="form-header">
			<h1 class="form-title">
				<i class="fas fa-plus me-2"></i>Создание поста
			</h1>
			<p class="form-subtitle">Поделитесь красивым местом Карелии с другими путешественниками</p>
		</div>

		<!-- Краткие правила публикации -->
		<div class="form-alert">
			<div class="alert-content">
				<i class="fas fa-info-circle me-2"></i>
				<div>
					<strong>Правила публикации:</strong> выберите правильную категорию, укажите точку на карте и добавьте качественные фото.
					<a href="{{ route('guidelines') }}" class="alert-link" target="_blank" rel="noopener">Подробные правила</a>
				</div>
			</div>
		</div>


		<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="post-create-form">
			@csrf
			<input type="hidden" name="status" id="status" value="moderation">
					<input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
					<input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
					<input type="hidden" name="main_index" id="main_index" value="{{ old('main_index', 0) }}">

			<div class="row g-4">
				<div class="col-lg-8">
					<div class="form-card">
						<div class="form-card-body">
							<div class="form-group">
								<label for="title" class="form-label">
									<i class="fas fa-heading me-1"></i>Название места
								</label>
								<input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" 
									   id="title" name="title" value="{{ old('title') }}" 
									   placeholder="Например: Остров Кижи, Водопад Кивач, Гора Сампо">
								<div class="form-text">Укажите точное и понятное название места</div>
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

							<div class="mb-3">
								<label for="website_url" class="form-label">Ссылка на сайт <span class="text-muted">(необязательно)</span></label>
								<input type="url" id="website_url" name="website_url" class="form-control @error('website_url') is-invalid @enderror" 
									   placeholder="https://example.com" value="{{ old('website_url') }}">
								<div class="form-text">Укажите ссылку на официальный сайт места или дополнительную информацию</div>
								@error('website_url')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="tags" class="form-label">
									<i class="fas fa-tags me-1"></i>Теги
								</label>
								<input
									type="text"
									name="tags"
									id="tags"
									class="form-control @error('tags') is-invalid @enderror"
									value="{{ old('tags') }}"
									placeholder="Например: водопады, зима, семейный отдых"
								>
								<div class="form-text">
									Введите теги через запятую. Не более 10 тегов, каждый до 30 символов.
								</div>
								@error('tags')
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
						<small class="form-text text-muted">
							Достопримечательности — природные/культурные объекты (🌄), Места отдыха — размещение/питание/зоны отдыха (🏨).
							<a href="{{ route('guidelines') }}" target="_blank" rel="noopener">Подробнее</a>
						</small>
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

						<div class="row g-2 mt-2">
							<div class="col-6">
								<label for="latitude_input" class="form-label small text-muted mb-0">Широта</label>
								<input type="number" step="0.00000001" min="-90" max="90" id="latitude_input" class="form-control" placeholder="61.78700000">
							</div>
							<div class="col-6">
								<label for="longitude_input" class="form-label small text-muted mb-0">Долгота</label>
								<input type="number" step="0.00000001" min="-180" max="180" id="longitude_input" class="form-control" placeholder="34.36400000">
							</div>
						</div>
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
							
							<div class="mt-3">
								<div class="form-check mb-3">
									<input class="form-check-input" type="checkbox" id="is_personal_photos" name="is_personal_photos" value="1" {{ old('is_personal_photos') ? 'checked' : '' }}>
									<label class="form-check-label" for="is_personal_photos">
										Это мои личные фотографии
									</label>
								</div>
								
								<div id="photo_source_wrapper">
									<label for="photo_source" class="form-label">Источник фотографий <span class="text-danger">*</span></label>
									<input type="url" id="photo_source" name="photo_source" class="form-control @error('photo_source') is-invalid @enderror" 
										   placeholder="https://example.com/photos" value="{{ old('photo_source') }}">
									<div class="form-text">Укажите ссылку на источник фотографий (если не ваши личные фото)</div>
									@error('photo_source')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
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
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
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
		<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
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

				// Autosave draft to localStorage
				const DRAFT_KEY = 'post_create_draft';
				function saveDraft() {
					const data = {
						title: document.getElementById('title')?.value || '',
						description: quill.root.innerHTML || '',
						category_id: document.getElementById('category_id')?.value || '',
						address: document.getElementById('address')?.value || '',
						website_url: document.getElementById('website_url')?.value || '',
						latitude: document.getElementById('latitude')?.value || '',
						longitude: document.getElementById('longitude')?.value || '',
						is_personal_photos: document.getElementById('is_personal_photos')?.checked || false,
						photo_source: document.getElementById('photo_source')?.value || ''
					};
					try { localStorage.setItem(DRAFT_KEY, JSON.stringify(data)); } catch (_) {}
				}
				function loadDraft() {
					try {
						const raw = localStorage.getItem(DRAFT_KEY);
						if (!raw) return;
						const d = JSON.parse(raw);
						if (d.title) document.getElementById('title').value = d.title;
						if (d.description) { quill.root.innerHTML = d.description; el.value = d.description; }
						if (d.category_id) document.getElementById('category_id').value = d.category_id;
						if (d.address) document.getElementById('address').value = d.address;
						if (d.website_url) document.getElementById('website_url').value = d.website_url;
						if (d.is_personal_photos !== undefined) {
							document.getElementById('is_personal_photos').checked = d.is_personal_photos;
							document.getElementById('is_personal_photos').dispatchEvent(new Event('change'));
						}
						if (d.photo_source) document.getElementById('photo_source').value = d.photo_source;
						if (d.latitude && d.longitude) {
							document.getElementById('latitude').value = d.latitude;
							document.getElementById('longitude').value = d.longitude;
							const latIn = document.getElementById('latitude_input');
							const lngIn = document.getElementById('longitude_input');
							if (latIn) latIn.value = d.latitude;
							if (lngIn) lngIn.value = d.longitude;
							if (typeof updateMapCenter === 'function') {
								updateMapCenter([parseFloat(d.latitude), parseFloat(d.longitude)]);
							}
						}
					} catch (_) {}
				}
				// Bind events for autosave
				['title','category_id','address','website_url','photo_source','latitude','longitude'].forEach(id => {
					const elx = document.getElementById(id);
					if (elx) elx.addEventListener('input', saveDraft);
				});
				const personalPhotosCheckbox = document.getElementById('is_personal_photos');
				if (personalPhotosCheckbox) {
					personalPhotosCheckbox.addEventListener('change', saveDraft);
				}
				const latManual = document.getElementById('latitude_input');
				const lngManual = document.getElementById('longitude_input');
				if (latManual) latManual.addEventListener('input', saveDraft);
				if (lngManual) lngManual.addEventListener('input', saveDraft);
				quill.on('text-change', saveDraft);
				window.addEventListener('load', loadDraft);
				document.getElementById('post-create-form').addEventListener('submit', function(){
					try { localStorage.removeItem(DRAFT_KEY); } catch (_) {}
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
				const latInput = document.getElementById('latitude_input');
				const lngInput = document.getElementById('longitude_input');
				if (latInput) latInput.value = coords[0].toFixed(8);
				if (lngInput) lngInput.value = coords[1].toFixed(8);
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

			// Двусторонняя связь: ручной ввод -> карта
			(function(){
				const latInput = document.getElementById('latitude_input');
				const lngInput = document.getElementById('longitude_input');
				function normalizeCoordInput(inputEl, min, max) {
					let v = (inputEl.value || '').toString().replace(',', '.').trim();
					v = v.replace(/[^0-9.\-]/g, '');
					const n = parseFloat(v);
					if (!isFinite(n)) return null;
					const clamped = Math.min(max, Math.max(min, n));
					inputEl.value = clamped.toFixed(8);
					return clamped;
				}
				function applyManualCoords(){
					const lat = normalizeCoordInput(latInput, -90, 90);
					const lng = normalizeCoordInput(lngInput, -180, 180);
					if (isFinite(lat) && isFinite(lng)) {
						updateMapCenter([lat, lng]);
					}
				}
				if (latInput) { latInput.addEventListener('change', applyManualCoords); latInput.addEventListener('blur', applyManualCoords); }
				if (lngInput) { lngInput.addEventListener('change', applyManualCoords); lngInput.addEventListener('blur', applyManualCoords); }
			})();

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

			// Обработка чекбокса "личные фотографии"
			(function() {
				const checkbox = document.getElementById('is_personal_photos');
				const wrapper = document.getElementById('photo_source_wrapper');
				const input = document.getElementById('photo_source');
				
				function togglePhotoSource() {
					if (checkbox.checked) {
						wrapper.style.display = 'none';
						input.removeAttribute('required');
						input.value = '';
					} else {
						wrapper.style.display = 'block';
						input.setAttribute('required', 'required');
					}
				}
				
				if (checkbox && wrapper && input) {
					checkbox.addEventListener('change', togglePhotoSource);
					togglePhotoSource(); // Инициализация при загрузке
				}
			})();

			// Теги: Tagify + AJAX‑подсказки
			(function () {
				const input = document.querySelector('input[name="tags"]');
				if (!input) return;

				const tagify = new Tagify(input, {
					maxTags: 10,
					originalInputValueFormat(valuesArr) {
						return valuesArr.map(item => item.value).join(', ');
					},
					dropdown: {
						enabled: 0,
						maxItems: 10,
						closeOnSelect: false,
						classname: 'tags-dropdown',
						highlightFirst: true,
					},
				});

				let controller = null;

				tagify.on('input', function (e) {
					const value = e.detail.value || '';

					if (value.length < 2) {
						tagify.settings.whitelist = [];
						tagify.dropdown.hide();
						return;
					}

					if (controller) controller.abort();
					controller = new AbortController();

					fetch("{{ route('tags.suggest') }}?q=" + encodeURIComponent(value), {
						signal: controller.signal,
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
						},
					})
						.then(res => res.json())
						.then(data => {
							tagify.settings.whitelist = data;
							tagify.dropdown.show.call(tagify, value);
						})
						.catch(err => {
							if (err.name === 'AbortError') return;
							console.error('Tagify suggest error', err);
						});
				});

				tagify.on('add', function (e) {
					const maxLength = 30;
					const val = e.detail.data.value || '';
					if (val.length > maxLength) {
						tagify.removeTag(e.detail.tag);
						alert('Тег "' + val + '" слишком длинный. Максимум ' + maxLength + ' символов.');
					}
				});
			})();

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
						maxFiles: 20,
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
