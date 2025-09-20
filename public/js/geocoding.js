/**
 * Функция геокодирования адреса с использованием API Яндекс.Карт
 * @param {string} address - Адрес для поиска
 * @param {function} callback - Функция обратного вызова (coords, foundAddress)
 * @param {object} options - Дополнительные опции
 */
function geocodeAddress(address, callback, options = {}) {
    const defaultOptions = {
        results: 1,
        kind: 'house',
        statusElement: null,
        showStatus: true
    };

    const opts = { ...defaultOptions, ...options };

    if (!address || address.trim() === '') {
        if (opts.showStatus && opts.statusElement) {
            showGeocodeStatus(opts.statusElement, 'Введите адрес для поиска', 'warning');
        }
        return Promise.reject('Пустой адрес');
    }

    if (opts.showStatus && opts.statusElement) {
        showGeocodeStatus(opts.statusElement, 'Поиск координат...', 'info');
    }

    return ymaps.geocode(address, {
        results: opts.results,
        kind: opts.kind
    }).then(function (res) {
        var first = res.geoObjects.get(0);
        if (!first) {
            if (opts.showStatus && opts.statusElement) {
                showGeocodeStatus(opts.statusElement, 'Адрес не найден. Попробуйте уточнить адрес.', 'danger');
            }
            return Promise.reject('Адрес не найден');
        }

        var coords = first.geometry.getCoordinates();
        var foundAddress = first.getAddressLine();

        if (opts.showStatus && opts.statusElement) {
            showGeocodeStatus(opts.statusElement, `Найдено: ${foundAddress}`, 'success');
        }

        if (callback) {
            callback(coords, foundAddress);
        }

        return { coords, foundAddress };
    }).catch(function (error) {
        console.error('Ошибка геокодирования:', error);
        if (opts.showStatus && opts.statusElement) {
            showGeocodeStatus(opts.statusElement, 'Ошибка при поиске адреса', 'danger');
        }
        return Promise.reject(error);
    });
}

/**
 * Функция показа статуса геокодирования
 * @param {HTMLElement} statusElement - Элемент для отображения статуса
 * @param {string} message - Сообщение
 * @param {string} type - Тип сообщения (success, danger, warning, info)
 * @param {number} timeout - Время автоматического скрытия (мс)
 */
function showGeocodeStatus(statusElement, message, type, timeout = 5000) {
    if (!statusElement) return;

    statusElement.innerHTML = `<span class="text-${type}"><i class="fas fa-${getStatusIcon(type)} me-1"></i>${message}</span>`;

    // Автоматически скрываем сообщение
    if (timeout > 0) {
        setTimeout(() => {
            statusElement.innerHTML = '';
        }, timeout);
    }
}

/**
 * Получение иконки для типа статуса
 * @param {string} type - Тип статуса
 * @returns {string} - Название иконки Font Awesome
 */
function getStatusIcon(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Инициализация геокодирования для формы
 * @param {object} config - Конфигурация
 */
function initGeocoding(config) {
    const {
        addressInput,
        geocodeButton,
        statusElement,
        latitudeInput,
        longitudeInput,
        map,
        marker,
        autoGeocode = true,
        minLength = 10,
        delay = 1000
    } = config;

    if (!addressInput || !geocodeButton || !statusElement) {
        console.error('Не все обязательные элементы найдены для инициализации геокодирования');
        return;
    }

    // Функция обновления карты
    function updateMapCenter(coords) {
        if (map && marker) {
            map.setCenter(coords, 14);
            marker.geometry.setCoordinates(coords);
        }

        if (latitudeInput && longitudeInput) {
            latitudeInput.value = coords[0].toFixed(8);
            longitudeInput.value = coords[1].toFixed(8);
        }
    }

    // Обработчик кнопки геокодирования
    geocodeButton.addEventListener('click', function () {
        const address = addressInput.value.trim();
        geocodeAddress(address, function (coords, foundAddress) {
            // Обновляем поле адреса найденным значением
            addressInput.value = foundAddress;
            // Обновляем карту
            updateMapCenter(coords);
        }, {
            statusElement: statusElement,
            showStatus: true
        });
    });

    // Обработчик Enter в поле адреса
    addressInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const address = this.value.trim();
            geocodeAddress(address, function (coords, foundAddress) {
                addressInput.value = foundAddress;
                updateMapCenter(coords);
            }, {
                statusElement: statusElement,
                showStatus: true
            });
        }
    });

    // Автоматическое геокодирование при изменении адреса
    if (autoGeocode) {
        let geocodeTimeout;
        addressInput.addEventListener('input', function () {
            clearTimeout(geocodeTimeout);
            geocodeTimeout = setTimeout(() => {
                if (this.value.trim().length >= minLength) {
                    geocodeAddress(this.value.trim(), function (coords, foundAddress) {
                        addressInput.value = foundAddress;
                        updateMapCenter(coords);
                    }, {
                        statusElement: statusElement,
                        showStatus: true
                    });
                }
            }, delay);
        });
    }
}

// Экспорт для использования в других модулях
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        geocodeAddress,
        showGeocodeStatus,
        getStatusIcon,
        initGeocoding
    };
}
