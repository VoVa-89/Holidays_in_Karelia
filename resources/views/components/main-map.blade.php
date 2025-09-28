@props(['posts'])

<div class="row mb-5">
    <div class="col-12">
        <h2 class="h4 mb-3">
            <i class="fas fa-map-marked-alt text-primary me-2"></i>Карта интересных мест
        </h2>
        
        <!-- Loader для карты -->
        <div id="main-map-loader" class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Загрузка карты...</span>
            </div>
            <p class="mt-3 text-muted">Загрузка карты интересных мест...</p>
        </div>
        
        <!-- Контейнер для карты -->
        <div id="yandex-map" 
             style="width: 100%; height: 520px; border-radius: 12px; overflow: hidden; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);display:none;">
        </div>
        
        <!-- Сообщение об ошибке загрузки карты -->
        <div id="main-map-error" class="alert alert-warning" style="display:none;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Ошибка загрузки карты</strong><br>
            <small>Не удалось загрузить интерактивную карту. 
            <a href="https://yandex.ru/maps/?ll=34.3466,61.7850&z=7&l=map" 
               target="_blank" class="alert-link">Открыть в Яндекс.Картах</a></small>
        </div>
        
        <!-- Fallback для отключенного JavaScript -->
        <noscript>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Статическая карта</strong><br>
                <small>JavaScript отключен. 
                <a href="https://yandex.ru/maps/?ll=34.3466,61.7850&z=7&l=map" 
                   target="_blank" class="alert-link">Открыть в Яндекс.Картах</a></small>
            </div>
            <div class="text-center py-3">
                <img src="https://static-maps.yandex.ru/1.x/?ll=34.3466,61.7850&z=7&l=map&size=800,520" 
                     alt="Карта Карелии" 
                     class="img-fluid rounded" 
                     style="max-width: 100%; height: 520px; object-fit: cover;">
            </div>
        </noscript>
        
        <!-- Подсказка для пользователей -->
        <div class="mt-4 mb-0" style="
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        ">
            <div class="d-flex align-items-start">
                <div class="me-3" style="
                    background: rgba(13, 202, 240, 0.15);
                    border-radius: 50%;
                    width: 40px;
                    height: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <i class="fas fa-mouse-pointer text-info" style="font-size: 1.1rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-3 text-dark fw-semibold">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Как пользоваться картой
                    </h6>
                    @if($posts->count() > 0)
                        <p class="mb-3 text-dark">
                            <strong>Показано {{ $posts->count() }} интересных мест</strong> в Карелии. 
                            <span class="text-primary fw-semibold">Кликните на любую метку</span> на карте, чтобы:
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-book-open text-success me-2"></i>
                                    <span class="small"><strong>Прочитать описание</strong> места</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    <span class="small"><strong>Узнать рейтинг</strong> и отзывы</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-external-link-alt text-primary me-2"></i>
                                    <span class="small"><strong>Перейти к полной странице</strong> места</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                <span class="small text-success fw-semibold">Зеленые метки</span>
                                <span class="small text-muted ms-1">— достопримечательности</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <span class="small text-primary fw-semibold">Синие метки</span>
                                <span class="small text-muted ms-1">— места отдыха</span>
                            </div>
                        </div>
                    @else
                        <p class="mb-3 text-dark">
                            Карта готова к отображению новых мест! 
                            <a href="{{ route('posts.create') }}" class="text-primary fw-bold text-decoration-none">Добавьте первое место</a> и оно появится на карте.
                        </p>
                        <p class="mb-0 small text-muted">
                            После добавления места вы сможете кликать на метки для просмотра подробной информации.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Глобальные переменные для карты
        let yandexMap;
        let mapClusterer;
        let mapLoadTimeout;

        // Функция показа ошибки загрузки карты
        function showMainMapError() {
            console.log('📸 Показываем ошибку загрузки карты');
            document.getElementById('main-map-loader').style.display = 'none';
            document.getElementById('main-map-error').style.display = 'block';
        }

        // Функция скрытия лоадера и показа карты
        function showMainMap() {
            console.log('🎉 Показываем карту!');
            document.getElementById('main-map-loader').style.display = 'none';
            document.getElementById('yandex-map').style.display = 'block';
        }

        // Функция инициализации карты
        function initMap() {
            console.log('🚀 Инициализация карты...');
            
            // Устанавливаем таймаут для загрузки карты (15 секунд)
            mapLoadTimeout = setTimeout(function() {
                console.error('⏰ Таймаут инициализации карты (15 секунд)');
                showMainMapError();
            }, 15000);

            // Проверяем, загружен ли API Яндекс.Карт
            if (typeof ymaps === 'undefined') {
                console.error('❌ API Яндекс.Карт не загружен в initMap()');
                showMainMapError();
                return;
            }
            
            console.log('✅ API Яндекс.Карт доступен, запускаем ymaps.ready()');

            try {
                ymaps.ready(function () {
                    console.log('🗺️ ymaps.ready() выполнен');
                    try {
                        // Проверяем существование контейнера
                        const container = document.getElementById('yandex-map');
                        if (!container) {
                            console.error('❌ Контейнер yandex-map не найден при создании карты!');
                            showMainMapError();
                            return;
                        }
                        
                        console.log('📍 Создаем карту с центром в Карелии...');
                        console.log('📦 Контейнер размеры:', container.offsetWidth, 'x', container.offsetHeight);
                        
                        yandexMap = new ymaps.Map('yandex-map', {
                            center: [61.7850, 34.3466], // Координаты Карелии
                            zoom: 7,
                            controls: [
                                'zoomControl', 
                                'geolocationControl', 
                                'fullscreenControl', 
                                'typeSelector'
                            ]
                        });
                        
                        console.log('✅ Карта создана успешно');

                        // Обработчик успешной загрузки карты
                        yandexMap.events.add('ready', function() {
                            console.log('🎉 Карта полностью готова!');
                            clearTimeout(mapLoadTimeout);
                            showMainMap();
                        });
                        
                        // Дополнительный таймаут для показа карты
                        setTimeout(function() {
                            if (yandexMap) {
                                console.log('⏰ Принудительно показываем карту через 3 секунды');
                                clearTimeout(mapLoadTimeout);
                                showMainMap();
                            }
                        }, 3000);

                        // Создаем кластеризатор
                        mapClusterer = new ymaps.Clusterer({
                            preset: 'islands#invertedGreenClusterIcons',
                            groupByCoordinates: false,
                            clusterDisableClickZoom: false,
                            clusterOpenBalloonOnClick: true,
                            clusterBalloonContentLayout: 'cluster#balloonCarousel'
                        });

                        // Добавляем кластеризатор на карту
                        yandexMap.geoObjects.add(mapClusterer);

                        // Добавляем метки из базы данных
                        @php
                            $markersData = $posts->map(function($p) {
                                return [
                                    'p' => [
                                        'id' => $p->id,
                                        'title' => $p->title,
                                        'slug' => $p->slug,
                                        'address' => $p->address,
                                        'rating' => $p->rating,
                                        'category' => optional($p->category)->name ?? '',
                                        'category_slug' => optional($p->category)->slug ?? '',
                                    ],
                                    'geo' => [(float)$p->latitude, (float)$p->longitude],
                                ];
                            });
                        @endphp
                        const markers = @json($markersData);

                        // Добавляем метки на карту
                        markers.forEach(function(marker) {
                            console.log('📍 Добавляем метку:', marker.p.title, 'Категория slug:', marker.p.category_slug);
                            addPlacemark(marker.geo, marker.p.title, marker.p.slug, marker.p);
                        });

                        // // Если есть метки, подгоняем карту под них
                        // if (markers.length > 0) {
                        //     yandexMap.setBounds(mapClusterer.getBounds(), { 
                        //         checkZoomRange: true, 
                        //         zoomMargin: 40 
                        //     });
                        // } else {
                        //     // Если меток нет, показываем всю Карелию
                        //     yandexMap.setCenter([61.7850, 34.3466]);
                        //     yandexMap.setZoom(7);
                        //     console.log('📍 Карта установлена на центр Карелии (нет меток)');
                        // }

                        // Всегда показываем фиксированный обзор Карелии
                        yandexMap.setCenter([61.7850, 34.3466]);
                        yandexMap.setZoom(8); // Ваш желаемый зум
                        console.log('📍 Карта установлена на центр Карелии с зумом 3')


                    } catch (error) {
                        console.error('Ошибка создания карты:', error);
                        clearTimeout(mapLoadTimeout);
                        showMainMapError();
                    }
                });
            } catch (error) {
                console.error('Ошибка инициализации ymaps:', error);
                clearTimeout(mapLoadTimeout);
                showMainMapError();
            }
        }

        // Функция добавления метки
        function addPlacemark(coordinates, title, postSlug, postData = null) {
            if (!yandexMap || !mapClusterer) return;

            // Создаем содержимое балуна
            let balloonContent = `
                <div style="min-width: 250px; padding: 10px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    <h6 class="mb-2" style="color: #333; font-weight: 600; margin: 0;">${title}</h6>
            `;

            if (postData) {
                if (postData.address) {
                    balloonContent += `
                        <div class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt" style="color: #007bff; margin-right: 5px;"></i>
                            ${postData.address}
                        </div>
                    `;
                }
                
                if (postData.category) {
                    // Определяем цвет категории в зависимости от типа
                    let categoryColor = '#17a2b8'; // По умолчанию синий
                    if (postData.category_slug === 'dostoprimechatelnosti') {
                        categoryColor = '#28a745'; // Зеленый для достопримечательностей
                    } else if (postData.category_slug === 'mesta-otdykha') {
                        categoryColor = '#007bff'; // Синий для мест отдыха
                    }
                    
                    balloonContent += `
                        <div class="mb-2">
                            <span style="background: ${categoryColor}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
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
            }

            balloonContent += `
                    <a href="/posts/${postSlug}" 
                       style="display: inline-block; background: #007bff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 500; transition: background-color 0.2s;">
                        <i class="fas fa-arrow-right" style="margin-right: 5px;"></i>Подробнее
                    </a>
                </div>
            `;

            // Определяем цвет метки в зависимости от категории
            let iconPreset = 'islands#greenIcon'; // По умолчанию зеленый
            console.log('🎨 Определяем цвет для:', title, 'category_slug:', postData?.category_slug);
            if (postData && postData.category_slug) {
                if (postData.category_slug === 'dostoprimechatelnosti') {
                    iconPreset = 'islands#greenIcon'; // Зеленый для достопримечательностей
                    console.log('✅ Установлен зеленый цвет для достопримечательности');
                } else if (postData.category_slug === 'mesta-otdykha') {
                    iconPreset = 'islands#blueIcon'; // Синий для мест отдыха
                    console.log('✅ Установлен синий цвет для места отдыха');
                }
            } else {
                console.log('⚠️ Нет данных о категории или category_slug пустой');
            }

            // Создаем метку
            const placemark = new ymaps.Placemark(coordinates, {
                balloonContent: balloonContent,
                hintContent: title
            }, {
                preset: iconPreset,
                balloonMaxWidth: 300,
                balloonCloseButton: true,
                balloonAutoPan: true
            });

            // Добавляем обработчик клика для открытия балуна (без перехода на страницу)
            placemark.events.add('click', function () {
                console.log('🎯 Клик по метке:', title);
                // Балун откроется автоматически, переход только по кнопке "Подробнее"
            });

            // Добавляем метку в кластеризатор
            mapClusterer.add(placemark);
        }

        // Простая и надежная инициализация карты
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🗺️ Главная карта: начинаем загрузку...');
            
            // Проверяем наличие контейнера карты
            const mapContainer = document.getElementById('yandex-map');
            if (!mapContainer) {
                console.error('❌ Контейнер #yandex-map не найден!');
                showMainMapError();
                return;
            }
            
            console.log('✅ Контейнер карты найден');
            
            // Функция загрузки и инициализации
            function loadAndInitMap() {
                console.log('📡 Загружаем API...');
                
                const script = document.createElement('script');
                script.src = 'https://api-maps.yandex.ru/2.1/?apikey=3c422f19-3fc8-4078-a90b-648002e366ad&lang=ru_RU';
                
                script.onload = function() {
                    console.log('✅ API загружен, ждем ymaps.ready()');
                    
                    ymaps.ready(function() {
                        console.log('🎯 ymaps.ready() выполнен, инициализируем карту');
                        initMap();
                    });
                };
                
                script.onerror = function() {
                    console.error('❌ Ошибка загрузки API');
                    showMainMapError();
                };
                
                document.head.appendChild(script);
            }
            
            // Проверяем, не загружен ли уже API
            if (typeof ymaps !== 'undefined') {
                console.log('✅ API уже загружен');
                ymaps.ready(function() {
                    initMap();
                });
            } else {
                loadAndInitMap();
            }
        });
    </script>
@endpush
