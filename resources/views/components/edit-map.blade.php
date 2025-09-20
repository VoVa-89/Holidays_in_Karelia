@props(['post'])

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3"><i class="fas fa-map-marked-alt me-2"></i>Карта</h5>
        
        <!-- Loader для карты -->
        <div id="edit-map-loader-{{ $post->id }}" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка карты...</span>
            </div>
            <p class="mt-2 text-muted">Загрузка карты...</p>
        </div>
        
        <!-- Контейнер для карты -->
        <div id="edit-map-{{ $post->id }}" style="width:100%;height:360px;border-radius:12px;overflow:hidden;display:none;"></div>
        
        <!-- Сообщение об ошибке загрузки карты -->
        <div id="edit-map-error-{{ $post->id }}" class="alert alert-warning" style="display:none;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Ошибка загрузки карты</strong><br>
            <small>Не удалось загрузить интерактивную карту. Вы можете указать координаты вручную или попробовать позже.</small>
        </div>
        
        <div class="small text-muted mt-2">Перетащите метку для уточнения координат.</div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🗺️ Инициализация карты редактирования для поста {{ $post->id }}');
    
    // Глобальные переменные для карты редактирования
    let editMap{{ $post->id }};
    let editMapLoadTimeout{{ $post->id }};
    
    // Функция показа ошибки загрузки карты
    function showEditMapError{{ $post->id }}() {
        document.getElementById('edit-map-loader-{{ $post->id }}').style.display = 'none';
        document.getElementById('edit-map-error-{{ $post->id }}').style.display = 'block';
    }
    
    // Функция скрытия лоадера и показа карты
    function showEditMap{{ $post->id }}() {
        document.getElementById('edit-map-loader-{{ $post->id }}').style.display = 'none';
        document.getElementById('edit-map-{{ $post->id }}').style.display = 'block';
    }
    
    function initEditMap{{ $post->id }}() {
        console.log('🚀 Инициализация карты редактирования {{ $post->id }}...');
        
        // Проверяем контейнер
        const container = document.getElementById('edit-map-{{ $post->id }}');
        if (!container) {
            console.error('❌ Контейнер edit-map-{{ $post->id }} не найден!');
            showEditMapError{{ $post->id }}();
            return;
        }
        
        console.log('✅ Контейнер карты редактирования найден');
        
        // Устанавливаем таймаут для загрузки карты (10 секунд)
        editMapLoadTimeout{{ $post->id }} = setTimeout(function() {
            console.error('⏰ Таймаут карты редактирования {{ $post->id }}');
            showEditMapError{{ $post->id }}();
        }, 10000);
        
        // Проверяем, загружен ли API Яндекс.Карт
        if (typeof ymaps === 'undefined') {
            console.error('❌ API Яндекс.Карт не загружен для редактирования {{ $post->id }}');
            showEditMapError{{ $post->id }}();
            return;
        }
        
        console.log('✅ API доступен для редактирования {{ $post->id }}');
        
        try {
            ymaps.ready(function(){
                console.log('🗺️ ymaps.ready() для редактирования {{ $post->id }}');
                try {
                    const lat = parseFloat(document.getElementById('latitude').value) || {{ $post->latitude ?? 61.787 }};
                    const lng = parseFloat(document.getElementById('longitude').value) || {{ $post->longitude ?? 34.364 }};
                    
                    console.log('📍 Координаты поста для редактирования:', lat, lng);
                    
                    editMap{{ $post->id }} = new ymaps.Map('edit-map-{{ $post->id }}', { 
                        center: [lat, lng], 
                        zoom: 14, 
                        controls: ['zoomControl','fullscreenControl', 'typeSelector'] 
                    });
                    
                    console.log('✅ Карта редактирования {{ $post->id }} создана');
                    
                    // Обработчик успешной загрузки карты
                    editMap{{ $post->id }}.events.add('ready', function() {
                        console.log('🎉 Карта редактирования {{ $post->id }} готова!');
                        clearTimeout(editMapLoadTimeout{{ $post->id }});
                        showEditMap{{ $post->id }}();
                    });
                    
                    // Принудительный показ через 3 секунды
                    setTimeout(function() {
                        if (editMap{{ $post->id }}) {
                            console.log('⏰ Принудительно показываем карту редактирования {{ $post->id }}');
                            clearTimeout(editMapLoadTimeout{{ $post->id }});
                            showEditMap{{ $post->id }}();
                        }
                    }, 3000);
                    
                    var marker = new ymaps.Placemark([lat, lng], {}, { 
                        draggable: true, 
                        preset: 'islands#redIcon' 
                    });
                    editMap{{ $post->id }}.geoObjects.add(marker);
                    
                    // Обработчик перетаскивания метки
                    marker.events.add('dragend', function(){ 
                        const coords = marker.geometry.getCoordinates();
                        document.getElementById('latitude').value = coords[0].toFixed(8);
                        document.getElementById('longitude').value = coords[1].toFixed(8);
                        console.log('📍 Координаты обновлены:', coords[0].toFixed(8), coords[1].toFixed(8));
                    });
                    
                    // Глобальная функция для обновления карты при геокодировании
                    window.updateEditMap{{ $post->id }} = function(coords) {
                        if (editMap{{ $post->id }} && marker) {
                            editMap{{ $post->id }}.setCenter(coords, 14);
                            marker.geometry.setCoordinates(coords);
                            console.log('🗺️ Карта обновлена по геокодированию:', coords);
                        }
                    };
                    
                } catch (error) {
                    console.error('Ошибка создания карты редактирования:', error);
                    clearTimeout(editMapLoadTimeout{{ $post->id }});
                    showEditMapError{{ $post->id }}();
                }
            });
        } catch (error) {
            console.error('Ошибка инициализации ymaps для редактирования:', error);
            clearTimeout(editMapLoadTimeout{{ $post->id }});
            showEditMapError{{ $post->id }}();
        }
    }
    
    // Используем глобальную функцию загрузки API если доступна
    if (typeof window.loadYandexMaps === 'function') {
        console.log('📡 Используем глобальную функцию загрузки API для редактирования {{ $post->id }}');
        window.loadYandexMaps(function() {
            ymaps.ready(function() {
                initEditMap{{ $post->id }}();
            });
        });
    } else if (typeof ymaps !== 'undefined') {
        console.log('✅ API уже загружен для редактирования {{ $post->id }}');
        ymaps.ready(function() {
            initEditMap{{ $post->id }}();
        });
    } else {
        // Fallback - прямая загрузка API
        console.log('⚠️ Fallback загрузка API для редактирования {{ $post->id }}');
        const script = document.createElement('script');
        const apiKey = window.yandexMapsKey || '{{ config("services.yandex.maps_key") }}';
        
        if (apiKey) {
            script.src = `https://api-maps.yandex.ru/2.1/?apikey=${apiKey}&lang=ru_RU`;
        } else {
            script.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
            console.warn('⚠️ API ключ не найден для карты редактирования {{ $post->id }}');
        }
        
        script.onload = function() {
            console.log('✅ API загружен для редактирования {{ $post->id }}');
            ymaps.ready(function() {
                initEditMap{{ $post->id }}();
            });
        };
        
        script.onerror = function() {
            console.error('❌ Ошибка загрузки API для редактирования {{ $post->id }}');
            showEditMapError{{ $post->id }}();
        };
        
        document.head.appendChild(script);
    }
});
</script>
@endpush
