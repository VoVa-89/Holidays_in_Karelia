// Глобальный JavaScript для работы с Яндекс.Картами
document.addEventListener('DOMContentLoaded', function () {
    console.log('🗺️ Инициализация Yandex Maps JS');

    // Загрузка API Яндекс Карт
    function loadYandexMaps(callback) {
        if (typeof ymaps !== 'undefined') {
            console.log('✅ ymaps уже загружен');
            if (callback) callback();
            return;
        }

        console.log('📡 Загружаем API Яндекс.Карт...');
        const script = document.createElement('script');
        script.src = `https://api-maps.yandex.ru/2.1/?apikey=${window.yandexMapsKey}&lang=ru_RU`;

        script.onload = function () {
            console.log('✅ API Яндекс.Карт загружен');
            if (callback) callback();
        };

        script.onerror = function () {
            console.error('❌ Ошибка загрузки API Яндекс.Карт');
        };

        document.head.appendChild(script);
    }

    // Инициализация карты для превью в формах
    let previewMap = null;
    let previewPlacemark = null;

    function initPreviewMap() {
        const mapContainer = document.getElementById('map-preview');
        if (!mapContainer) return;

        const latitude = parseFloat(document.getElementById('latitude').value) || 61.7850;
        const longitude = parseFloat(document.getElementById('longitude').value) || 34.3466;

        previewMap = new ymaps.Map('map-preview', {
            center: [latitude, longitude],
            zoom: 10,
            controls: ['zoomControl', 'typeSelector']
        });

        if (!isNaN(latitude) && !isNaN(longitude)) {
            previewPlacemark = new ymaps.Placemark([latitude, longitude], {}, {
                draggable: true,
                preset: 'islands#redIcon'
            });
            previewMap.geoObjects.add(previewPlacemark);

            // Обработка перемещения метки
            previewPlacemark.events.add('dragend', function () {
                const coords = previewPlacemark.geometry.getCoordinates();
                document.getElementById('latitude').value = coords[0].toFixed(8);
                document.getElementById('longitude').value = coords[1].toFixed(8);

                // Геокодирование для получения адреса
                ymaps.geocode(coords).then(function (res) {
                    const firstGeoObject = res.geoObjects.get(0);
                    if (firstGeoObject) {
                        document.getElementById('address').value = firstGeoObject.getAddressLine();
                    }
                });
            });
        }

        mapContainer.style.display = 'block';
    }

    // Кнопка "Найти на карте"
    const findOnMapBtn = document.getElementById('find-on-map');
    if (findOnMapBtn) {
        findOnMapBtn.addEventListener('click', function () {
            loadYandexMaps(function () {
                ymaps.ready(function () {
                    initPreviewMap();

                    const address = document.getElementById('address').value;
                    if (address) {
                        ymaps.geocode(address).then(function (res) {
                            const firstGeoObject = res.geoObjects.get(0);
                            if (firstGeoObject) {
                                const coords = firstGeoObject.geometry.getCoordinates();
                                previewMap.setCenter(coords, 15);

                                if (previewPlacemark) {
                                    previewMap.geoObjects.remove(previewPlacemark);
                                }

                                previewPlacemark = new ymaps.Placemark(coords, {}, {
                                    draggable: true,
                                    preset: 'islands#redIcon'
                                });
                                previewMap.geoObjects.add(previewPlacemark);

                                document.getElementById('latitude').value = coords[0].toFixed(8);
                                document.getElementById('longitude').value = coords[1].toFixed(8);
                            }
                        });
                    }
                });
            });
        });
    }

    // Кнопка "Текущее местоположение"
    const getCurrentLocationBtn = document.getElementById('get-current-location');
    if (getCurrentLocationBtn) {
        getCurrentLocationBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Геолокация не поддерживается вашим браузером');
                return;
            }

            navigator.geolocation.getCurrentPosition(function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                document.getElementById('latitude').value = latitude.toFixed(8);
                document.getElementById('longitude').value = longitude.toFixed(8);

                loadYandexMaps(function () {
                    ymaps.ready(function () {
                        initPreviewMap();
                        previewMap.setCenter([latitude, longitude], 15);

                        if (previewPlacemark) {
                            previewMap.geoObjects.remove(previewPlacemark);
                        }

                        previewPlacemark = new ymaps.Placemark([latitude, longitude], {}, {
                            draggable: true,
                            preset: 'islands#redIcon'
                        });
                        previewMap.geoObjects.add(previewPlacemark);

                        // Получение адреса по координатам
                        ymaps.geocode([latitude, longitude]).then(function (res) {
                            const firstGeoObject = res.geoObjects.get(0);
                            if (firstGeoObject) {
                                document.getElementById('address').value = firstGeoObject.getAddressLine();
                            }
                        });
                    });
                });
            }, function (error) {
                alert('Не удалось получить ваше местоположение: ' + error.message);
            });
        });
    }

    // Экспортируем функцию для использования в других скриптах
    window.loadYandexMaps = loadYandexMaps;
});
