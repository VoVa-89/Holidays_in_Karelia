@extends('layouts.app')

@section('title', 'Редактировать пост — Отдых в Карелии')
@section('description', 'Редактирование поста: название, описание, категория, адрес, фото и координаты.')

@section('content')
	<div class="container my-4">
		<h1 class="h3 mb-3"><i class="fas fa-edit text-primary me-2"></i>Редактирование поста</h1>


		<form method="POST" action="{{ route('posts.update', $post->slug) }}" enctype="multipart/form-data" id="post-edit-form">
			@csrf
			@method('PUT')
			<input type="hidden" name="status" id="status" value="moderation">
			<input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $post->latitude) }}">
			<input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $post->longitude) }}">
			<input type="hidden" name="main_index" id="main_index" value="{{ old('main_index', $post->photos->where('is_main', true)->first()?->id ?? 0) }}">
			<input type="hidden" name="main_photo_id" id="main_photo_id" value="{{ old('main_photo_id', $post->photos->where('is_main', true)->first()?->id ?? '') }}">
			<input type="hidden" name="deleted_photos" id="deleted_photos" value="">

			<div class="row g-4">
				<div class="col-lg-8">
					<div class="card mb-4">
						<div class="card-body">
							<div class="mb-3">
								<label for="title" class="form-label">Название</label>
								<input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $post->title) }}" placeholder="Например: Остров Кижи">
								@error('title')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="description" class="form-label">Описание</label>
								<textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="8">{{ old('description', $post->description) }}</textarea>
								@error('description')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="website_url" class="form-label">Ссылка на сайт <span class="text-muted">(необязательно)</span></label>
								<input type="url" id="website_url" name="website_url" class="form-control @error('website_url') is-invalid @enderror" 
									   placeholder="https://example.com" value="{{ old('website_url', $post->website_url) }}">
								<div class="form-text">Укажите ссылку на официальный сайт места или дополнительную информацию</div>
								@error('website_url')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="row g-3">
								<div class="col-md-6">
									<label for="category_id" class="form-label">Категория</label>
									<select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
										<option value="" disabled>Выберите категорию</option>
										@foreach($categories as $category)
											<option value="{{ $category->id }}" {{ (int)old('category_id', $post->category_id) === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
										@endforeach
									</select>
									@error('category_id')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
								<div class="col-md-6">
									<label for="address" class="form-label">Адрес</label>
									<div class="input-group">
										<input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $post->address) }}" placeholder="Начните вводить адрес...">
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
							
							<!-- Существующие фотографии -->
							@if($post->photos->count() > 0)
								<div class="mb-3">
									<h6 class="text-muted">Текущие фотографии</h6>
									<div id="existing-photos" class="row g-2">
										@foreach($post->photos as $photo)
											<div class="col-6 col-md-4 col-lg-3" data-photo-id="{{ $photo->id }}">
												<div class="card existing-photo-card">
													<div class="position-relative">
														<img src="{{ asset($photo->photo_path) }}" alt="Фото {{ $loop->iteration }}" class="card-img-top" style="height: 120px; object-fit: cover;">
														<div class="main-badge position-absolute top-0 start-0 m-2">
															<span class="badge {{ $photo->is_main ? 'bg-primary' : 'bg-secondary' }}" style="cursor: pointer;">
																{{ $photo->is_main ? 'Основное' : 'Сделать основным' }}
															</span>
														</div>
														<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 delete-photo" data-photo-id="{{ $photo->id }}" title="Удалить фото">
															<i class="fas fa-times"></i>
														</button>
													</div>
													<div class="card-body p-2">
														<small class="text-muted">Фото {{ $loop->iteration }}</small>
													</div>
												</div>
											</div>
										@endforeach
									</div>
								</div>
							@endif

							<!-- Загрузка новых фотографий -->
							<div class="mb-3">
								<h6 class="text-muted">Добавить новые фотографии</h6>
								<input type="file" name="photos[]" id="photos" class="form-control" accept="image/*" multiple>
								<div class="form-text">Выберите дополнительные изображения.</div>
								<div id="photos-preview" class="row g-2 mt-2"></div>
							</div>
						</div>
					</div>

					<!-- Компонент карты редактирования -->
					<x-edit-map :post="$post" />
				</div>

				<div class="col-lg-4">
					<div class="card mb-4">
						<div class="card-body">
							<h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Информация о посте</h5>
							<div class="small text-muted mb-3">
								<p><strong>Статус:</strong> {{ ucfirst($post->status) }}</p>
								<p><strong>Создан:</strong> {{ $post->created_at->format('d.m.Y H:i') }}</p>
								<p><strong>Просмотры:</strong> {{ $post->views }}</p>
								@if($post->rating > 0)
									<p><strong>Рейтинг:</strong> {{ number_format($post->rating, 1) }}/5</p>
								@endif
							</div>
						</div>
					</div>

					<div class="card mb-4">
						<div class="card-body">
							<h5 class="mb-3"><i class="fas fa-save me-2"></i>Сохранение</h5>
							<p class="text-muted mb-3">Изменения будут отправлены на повторную модерацию.</p>
							<button type="submit" class="btn btn-primary w-100 mb-2">
								<i class="fas fa-save me-2"></i>Сохранить изменения
							</button>
							<a href="{{ route('posts.show', $post->slug) }}" class="btn btn-outline-secondary w-100">
								<i class="fas fa-times me-2"></i>Отмена
							</a>
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
			.existing-photo-card {
				transition: transform 0.2s ease, box-shadow 0.2s ease;
			}
			.existing-photo-card:hover {
				transform: translateY(-2px);
				box-shadow: 0 4px 8px rgba(0,0,0,0.15);
			}
			.existing-photo-card .card-img-top {
				border-radius: 0.375rem 0.375rem 0 0;
			}
			.delete-photo { z-index: 10; }
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
                document.getElementById('post-edit-form').addEventListener('submit', function(){
                    el.value = quill.root.innerHTML;

                    // Преобразуем main_index для новых фото в формат new_{index}, как ожидает backend
                    const mi = document.getElementById('main_index');
                    if (mi && mi.value !== '' && !isNaN(mi.value)) {
                        mi.value = 'new_' + mi.value;
                    }
                });

				// Autosave draft for edit
				const DRAFT_KEY = 'post_edit_draft_{{ $post->id }}';
				function saveDraft() {
					const data = {
						title: document.getElementById('title')?.value || '',
						description: quill.root.innerHTML || '',
						category_id: document.getElementById('category_id')?.value || '',
						address: document.getElementById('address')?.value || '',
						website_url: document.getElementById('website_url')?.value || '',
						latitude: document.getElementById('latitude')?.value || '',
						longitude: document.getElementById('longitude')?.value || ''
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
						if (d.latitude && d.longitude) {
							document.getElementById('latitude').value = d.latitude;
							document.getElementById('longitude').value = d.longitude;
							const latIn = document.getElementById('latitude_input_edit_{{ $post->id }}');
							const lngIn = document.getElementById('longitude_input_edit_{{ $post->id }}');
							if (latIn) latIn.value = d.latitude;
							if (lngIn) lngIn.value = d.longitude;
							if (typeof window.updateEditMap{{ $post->id }} === 'function') {
								window.updateEditMap{{ $post->id }}([parseFloat(d.latitude), parseFloat(d.longitude)]);
							}
						}
					} catch (_) {}
				}
				['title','category_id','address','website_url','latitude','longitude'].forEach(id => {
					const elx = document.getElementById(id);
					if (elx) elx.addEventListener('input', saveDraft);
				});
				const latManual = document.getElementById('latitude_input_edit_{{ $post->id }}');
				const lngManual = document.getElementById('longitude_input_edit_{{ $post->id }}');
				if (latManual) latManual.addEventListener('input', saveDraft);
				if (lngManual) lngManual.addEventListener('input', saveDraft);
				quill.on('text-change', saveDraft);
				window.addEventListener('load', loadDraft);
				document.getElementById('post-edit-form').addEventListener('submit', function(){
					try { localStorage.removeItem(DRAFT_KEY); } catch (_) {}
				});
			})();

			// Геокодирование адреса (без карты - карта в компоненте)
			document.addEventListener('DOMContentLoaded', function() {
				// Функция геокодирования адреса
				function geocodeAddress(address, callback) {
					if (!address || address.trim() === '') {
						showGeocodeStatus('Введите адрес для поиска', 'warning');
						return;
					}

					showGeocodeStatus('Поиск координат...', 'info');
					
					// Проверяем, загружен ли API
					if (typeof ymaps === 'undefined') {
						showGeocodeStatus('Карта еще загружается, попробуйте позже', 'warning');
						return;
					}
					
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
						
						// Обновляем скрытые поля координат
						document.getElementById('latitude').value = coords[0].toFixed(8);
						document.getElementById('longitude').value = coords[1].toFixed(8);
						
						// Обновляем карту если функция доступна
						if (typeof window.updateEditMap{{ $post->id }} === 'function') {
							window.updateEditMap{{ $post->id }}(coords);
						}
						
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
					if (statusEl) {
						statusEl.innerHTML = `<span class="text-${type}">${message}</span>`;
						
						// Автоматически скрываем сообщение через 5 секунд
						setTimeout(() => {
							statusEl.innerHTML = '';
						}, 5000);
					}
				}

				// Обработчик кнопки геокодирования
				const geocodeBtn = document.getElementById('geocode-btn');
				if (geocodeBtn) {
					geocodeBtn.addEventListener('click', function() {
						const address = document.getElementById('address').value.trim();
						geocodeAddress(address);
					});
				}

				// Обработчик Enter в поле адреса
				const addressInput = document.getElementById('address');
				if (addressInput) {
					addressInput.addEventListener('keypress', function(e) {
						if (e.key === 'Enter') {
							e.preventDefault();
							geocodeAddress(this.value.trim());
						}
					});

					// Автоматическое геокодирование при изменении адреса (с задержкой)
					let geocodeTimeout;
					addressInput.addEventListener('input', function() {
						clearTimeout(geocodeTimeout);
						geocodeTimeout = setTimeout(() => {
							if (this.value.trim().length > 10) { // Минимум 10 символов для автопоиска
								geocodeAddress(this.value.trim());
							}
						}, 1000); // Задержка 1 секунда
					});
				}
			});

			// Photos management: existing + new uploads
			setTimeout(function() {
				console.log('📷 === ЗАПУСК УПРАВЛЕНИЯ ФОТОГРАФИЯМИ ===');
				
				// Проверяем элементы
				const deleteButtons = document.querySelectorAll('.delete-photo');
				const mainBadges = document.querySelectorAll('.main-badge .badge');
				const input = document.getElementById('photos');
				const preview = document.getElementById('photos-preview');
				const deletedPhotos = document.getElementById('deleted_photos');
				const mainPhotoId = document.getElementById('main_photo_id');
				const mainIndex = document.getElementById('main_index');
				
				console.log('🔍 Найдено элементов:', {
					deleteButtons: deleteButtons.length,
					mainBadges: mainBadges.length,
					input: !!input,
					preview: !!preview,
					deletedPhotos: !!deletedPhotos,
					mainPhotoId: !!mainPhotoId,
					mainIndex: !!mainIndex
				});

				// === УДАЛЕНИЕ СУЩЕСТВУЮЩИХ ФОТО ===
                deleteButtons.forEach(function(btn, i) {
                    console.log(`🗑️ Кнопка удаления ${i + 1}:`, btn.dataset.photoId);
                    
                    btn.onclick = function() {
                        const photoId = this.dataset.photoId;
                        console.log('🎯 КЛИК УДАЛЕНИЯ:', photoId);

                        // блокируем повторные клики
                        if (this.dataset.deleted === '1') {
                            return;
                        }
                        
                        if (confirm('Удалить фотографию?')) {
                            const card = this.closest('[data-photo-id]');
                            
                            // Добавляем в список удаляемых
                            const deleted = deletedPhotos.value ? deletedPhotos.value.split(',') : [];
                            deleted.push(photoId);
                            deletedPhotos.value = deleted.join(',');
                            
                            // Визуальные изменения
                            if (card) {
                                card.style.opacity = '0.3';
                                card.style.filter = 'grayscale(100%)';
                                
                                // Добавляем метку
                                const label = document.createElement('div');
                                label.className = 'position-absolute top-50 start-50 translate-middle';
                                label.innerHTML = '<span class="badge bg-danger">УДАЛЕНО</span>';
                                const rel = card.querySelector('.position-relative') || card;
                                rel.appendChild(label);
                            }

                            this.dataset.deleted = '1';
                            
                            console.log('✅ Фото помечено для удаления:', photoId);
                        }
                    };
                });

				// === ВЫБОР ОСНОВНОЙ ФОТО ===
				mainBadges.forEach(function(badge, i) {
					console.log(`⭐ Бейдж ${i + 1}`);
					
					badge.onclick = function() {
						const photoId = this.closest('[data-photo-id]').dataset.photoId;
						console.log('🎯 КЛИК ОСНОВНАЯ ФОТО:', photoId);
						
						// Сбрасываем все бейджи
						document.querySelectorAll('.main-badge .badge').forEach(function(b) {
							b.className = 'badge bg-secondary';
							b.textContent = 'Сделать основным';
						});
						
						// Активируем текущий
						this.className = 'badge bg-primary';
						this.textContent = 'Основное';
						
						// Обновляем поля
						if (mainPhotoId) mainPhotoId.value = photoId;
						
						console.log('✅ Основная фото установлена:', photoId);
					};
				});

				// === НОВЫЕ ФОТОГРАФИИ С PHOTOPREVIEW ===
                if (input && preview && mainIndex) {
					console.log('📷 Инициализируем PhotoPreview для новых фото');
					
					// Инициализируем PhotoPreview для новых фотографий
					window.photoPreviewEdit = new PhotoPreview({
						input: input,
						preview: preview,
						mainIndexInput: mainIndex,
						maxFiles: 20,
						maxFileSize: 5 * 1024 * 1024, // 5MB
						allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
					});
					
					// Включаем drag & drop
                    window.photoPreviewEdit.enableDragDrop();

                    // Алиас для inline-обработчиков в шаблоне превью
                    window.photoPreview = window.photoPreviewEdit;
				}
				
				console.log('📷 === ИНИЦИАЛИЗАЦИЯ ЗАВЕРШЕНА ===');
			}, 2000);


		</script>
	@endpush
@endsection
