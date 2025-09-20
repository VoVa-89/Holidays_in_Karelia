@props(['post'])

@if($post->latitude && $post->longitude)
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="fas fa-map-marked-alt me-2"></i>На карте
            </h5>
            
            <!-- Loader для карты -->
            <div id="map-loader-{{ $post->id }}" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка карты...</span>
                </div>
                <p class="mt-2 text-muted">Загрузка карты...</p>
            </div>
            
            <!-- Контейнер для карты -->
            <div id="post-map-{{ $post->id }}" 
                 style="width:100%;height:380px;border-radius:12px;overflow:hidden;box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,.1);display:none;">
            </div>
            
            <!-- Сообщение об ошибке загрузки карты -->
            <div id="map-error-{{ $post->id }}" class="alert alert-warning" style="display:none;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Ошибка загрузки карты</strong><br>
                <small>Не удалось загрузить интерактивную карту. 
                <a href="https://yandex.ru/maps/?ll={{ $post->longitude }},{{ $post->latitude }}&z=14&l=map" 
                   target="_blank" class="alert-link">Открыть в Яндекс.Картах</a></small>
            </div>
            
            <!-- Fallback для отключенного JavaScript -->
            <noscript>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Статическая карта</strong><br>
                    <small>JavaScript отключен. 
                    <a href="https://yandex.ru/maps/?ll={{ $post->longitude }},{{ $post->latitude }}&z=14&l=map" 
                       target="_blank" class="alert-link">Открыть в Яндекс.Картах</a></small>
                </div>
                <div class="text-center py-3">
                    <img src="https://static-maps.yandex.ru/1.x/?ll={{ $post->longitude }},{{ $post->latitude }}&z=14&l=map&size=600,380&pt={{ $post->longitude }},{{ $post->latitude }},pm2rdm" 
                         alt="Карта: {{ $post->title }}" 
                         class="img-fluid rounded" 
                         style="max-width: 100%; height: 380px; object-fit: cover;">
                </div>
            </noscript>
            
            <div class="small text-muted mt-2">
                <i class="fas fa-info-circle me-1"></i>
                Координаты: {{ number_format($post->latitude, 6) }}, {{ number_format($post->longitude, 6) }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Глобальные переменные для карты поста
            let postMap{{ $post->id }};
            let mapLoadTimeout{{ $post->id }};

            // Функция показа ошибки загрузки карты
            function showMapError{{ $post->id }}() {
                document.getElementById('map-loader-{{ $post->id }}').style.display = 'none';
                document.getElementById('map-error-{{ $post->id }}').style.display = 'block';
            }

            // Функция скрытия лоадера и показа карты
            function showMap{{ $post->id }}() {
                document.getElementById('map-loader-{{ $post->id }}').style.display = 'none';
                document.getElementById('post-map-{{ $post->id }}').style.display = 'block';
            }

            // Функция инициализации карты поста
            function initPostMap{{ $post->id }}() {
                console.log('🚀 Инициализация карты поста {{ $post->id }}...');
                
                // Проверяем контейнер
                const container = document.getElementById('post-map-{{ $post->id }}');
                if (!container) {
                    console.error('❌ Контейнер post-map-{{ $post->id }} не найден!');
                    showMapError{{ $post->id }}();
                    return;
                }
                
                console.log('✅ Контейнер карты поста найден');
                
                // Устанавливаем таймаут для загрузки карты (10 секунд)
                mapLoadTimeout{{ $post->id }} = setTimeout(function() {
                    console.error('⏰ Таймаут карты поста {{ $post->id }}');
                    showMapError{{ $post->id }}();
                }, 10000);

                // Проверяем, загружен ли API Яндекс.Карт
                if (typeof ymaps === 'undefined') {
                    console.error('❌ API Яндекс.Карт не загружен для поста {{ $post->id }}');
                    showMapError{{ $post->id }}();
                    return;
                }
                
                console.log('✅ API доступен для поста {{ $post->id }}');

                try {
                    ymaps.ready(function () {
                        console.log('🗺️ ymaps.ready() для поста {{ $post->id }}');
                        try {
                            // Создаем карту с центром на координатах поста
                            console.log('📍 Создаем карту поста с координатами: {{ $post->latitude }}, {{ $post->longitude }}');
                            
                            postMap{{ $post->id }} = new ymaps.Map('post-map-{{ $post->id }}', {
                                center: [{{ (float)$post->latitude }}, {{ (float)$post->longitude }}],
                                zoom: 14,
                                controls: [
                                    'zoomControl', 
                                    'geolocationControl', 
                                    'fullscreenControl',
                                    'typeSelector'
                                ]
                            });
                            
                            console.log('✅ Карта поста {{ $post->id }} создана');

                            // Обработчик успешной загрузки карты
                            postMap{{ $post->id }}.events.add('ready', function() {
                                console.log('🎉 Карта поста {{ $post->id }} готова!');
                                clearTimeout(mapLoadTimeout{{ $post->id }});
                                showMap{{ $post->id }}();
                            });
                            
                            // Принудительный показ через 3 секунды
                            setTimeout(function() {
                                if (postMap{{ $post->id }}) {
                                    console.log('⏰ Принудительно показываем карту поста {{ $post->id }}');
                                    clearTimeout(mapLoadTimeout{{ $post->id }});
                                    showMap{{ $post->id }}();
                                }
                            }, 3000);

                            // Добавляем метку поста
                            addPostPlacemark{{ $post->id }}(
                                [{{ (float)$post->latitude }}, {{ (float)$post->longitude }}], 
                                '{{ addslashes($post->title) }}',
                                '{{ $post->slug }}',
                                {
                                    address: '{{ addslashes($post->address) }}',
                                    rating: {{ $post->rating }},
                                    category: '{{ addslashes($post->category->name) }}',
                                    author: '{{ addslashes($post->user->name) }}',
                                    created_at: '{{ $post->created_at->format('d.m.Y') }}'
                                }
                            );
                        } catch (error) {
                            console.error('Ошибка создания карты:', error);
                            clearTimeout(mapLoadTimeout{{ $post->id }});
                            showMapError{{ $post->id }}();
                        }
                    });
                } catch (error) {
                    console.error('Ошибка инициализации ymaps:', error);
                    clearTimeout(mapLoadTimeout{{ $post->id }});
                    showMapError{{ $post->id }}();
                }
            }

            // Функция добавления метки поста
            function addPostPlacemark{{ $post->id }}(coordinates, title, postSlug, postData = null) {
                if (!postMap{{ $post->id }}) return;

                // Создаем содержимое балуна
                let balloonContent = `
                    <div style="min-width: 300px; padding: 15px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                        <h5 class="mb-2" style="color: #333; font-weight: 600; margin: 0;">${title}</h5>
                `;

                if (postData) {
                    if (postData.address) {
                        balloonContent += `
                            <div class="mb-2" style="color: #666; font-size: 14px;">
                                <i class="fas fa-map-marker-alt" style="color: #007bff; margin-right: 5px;"></i>
                                ${postData.address}
                            </div>
                        `;
                    }
                    
                    if (postData.category) {
                        balloonContent += `
                            <div class="mb-2">
                                <span style="background: #17a2b8; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                    <i class="fas fa-folder" style="margin-right: 4px;"></i>${postData.category}
                                </span>
                            </div>
                        `;
                    }
                    
                    if (postData.rating && postData.rating > 0) {
                        balloonContent += `
                            <div class="mb-2">
                                <span style="background: #ffc107; color: #212529; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                    <i class="fas fa-star" style="margin-right: 4px;"></i>${parseFloat(postData.rating).toFixed(1)}
                                </span>
                            </div>
                        `;
                    }

                    if (postData.author) {
                        balloonContent += `
                            <div class="mb-2" style="color: #666; font-size: 13px;">
                                <i class="fas fa-user" style="color: #6c757d; margin-right: 5px;"></i>
                                Автор: ${postData.author}
                            </div>
                        `;
                    }

                    if (postData.created_at) {
                        balloonContent += `
                            <div class="mb-3" style="color: #666; font-size: 13px;">
                                <i class="fas fa-calendar" style="color: #6c757d; margin-right: 5px;"></i>
                                ${postData.created_at}
                            </div>
                        `;
                    }
                }

                balloonContent += `
                        <a href="/posts/${postSlug}" 
                           style="display: inline-block; background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500; transition: background-color 0.2s;">
                            <i class="fas fa-arrow-right" style="margin-right: 5px;"></i>Подробнее
                        </a>
                    </div>
                `;

                // Создаем метку
                const placemark = new ymaps.Placemark(coordinates, {
                    balloonContent: balloonContent,
                    hintContent: title
                }, {
                    preset: 'islands#redIcon',
                    balloonMaxWidth: 400,
                    balloonCloseButton: true,
                    balloonAutoPan: true
                });

                // Добавляем обработчик клика для открытия балуна (без перехода на страницу)
                placemark.events.add('click', function () {
                    console.log('🎯 Клик по метке поста:', title);
                    // Балун откроется автоматически, переход только по кнопке "Подробнее"
                });

                // Добавляем метку на карту
                postMap{{ $post->id }}.geoObjects.add(placemark);

                // Открываем балун автоматически
                placemark.balloon.open();
            }

            // Инициализируем карту при загрузке страницы
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🗺️ Карта поста {{ $post->id }}: начинаем загрузку...');
                
                // Используем глобальную функцию загрузки API если доступна
                if (typeof window.loadYandexMaps === 'function') {
                    console.log('📡 Используем глобальную функцию для поста {{ $post->id }}');
                    window.loadYandexMaps(function() {
                        ymaps.ready(function() {
                            initPostMap{{ $post->id }}();
                        });
                    });
                } else if (typeof ymaps !== 'undefined') {
                    console.log('✅ API уже загружен для поста {{ $post->id }}');
                    ymaps.ready(function() {
                        initPostMap{{ $post->id }}();
                    });
                } else {
                    // Fallback - прямая загрузка API
                    console.log('⚠️ Fallback загрузка API для поста {{ $post->id }}');
                    const script = document.createElement('script');
                    const apiKey = window.yandexMapsKey || '3c422f19-3fc8-4078-a90b-648002e366ad';
                    script.src = `https://api-maps.yandex.ru/2.1/?apikey=${apiKey}&lang=ru_RU`;
                    
                    script.onload = function() {
                        console.log('✅ API загружен для поста {{ $post->id }}');
                        ymaps.ready(function() {
                            initPostMap{{ $post->id }}();
                        });
                    };
                    
                    script.onerror = function() {
                        console.error('❌ Ошибка загрузки API для поста {{ $post->id }}');
                        showMapError{{ $post->id }}();
                    };
                    
                    document.head.appendChild(script);
                }
            });
        </script>
    @endpush
@else
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="fas fa-map-marked-alt me-2"></i>На карте
            </h5>
            <div class="text-center py-4">
                <i class="fas fa-map-marked-alt text-muted fa-3x mb-3"></i>
                <p class="text-muted mb-0">Координаты для этого места не указаны</p>
            </div>
        </div>
    </div>
@endif
