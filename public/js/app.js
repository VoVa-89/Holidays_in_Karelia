/**
 * Основной JavaScript файл для "Отдых в Карелии"
 */

// ===== ИНИЦИАЛИЗАЦИЯ =====
document.addEventListener('DOMContentLoaded', function () {
    initializeNavigation();
    initializeModals();
    initializeTooltips();
    initializeAlerts();
    initializeForms();
    initializeAdmin();
});

// ===== НАВИГАЦИЯ =====
function initializeNavigation() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarNav = document.querySelector('.navbar-nav');

    if (navbarToggler && navbarNav) {
        navbarToggler.addEventListener('click', function () {
            navbarToggler.classList.toggle('active');
            navbarNav.classList.toggle('active');
        });

        // Закрытие меню при клике вне его
        document.addEventListener('click', function (e) {
            if (!navbarToggler.contains(e.target) && !navbarNav.contains(e.target)) {
                navbarToggler.classList.remove('active');
                navbarNav.classList.remove('active');
            }
        });
    }
}

// ===== МОДАЛЬНЫЕ ОКНА =====
function initializeModals() {
    // Открытие модальных окон
    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-modal-target');
            const modal = document.getElementById(targetId);
            if (modal) {
                openModal(modal);
            }
        });
    });

    // Закрытие модальных окон
    document.querySelectorAll('.modal-close, [data-modal-close]').forEach(closeBtn => {
        closeBtn.addEventListener('click', function () {
            const modal = this.closest('.modal-overlay');
            if (modal) {
                closeModal(modal);
            }
        });
    });

    // Закрытие по клику на overlay
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });

    // Закрытие по Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal-overlay.active');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
}

function openModal(modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// ===== ТУЛТИПЫ =====
function initializeTooltips() {
    document.querySelectorAll('[data-tooltip]').forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const text = e.target.getAttribute('data-tooltip');
    if (!text) return;

    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 0.85rem;
        z-index: 1000;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    `;

    document.body.appendChild(tooltip);

    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

    setTimeout(() => tooltip.style.opacity = '1', 10);

    e.target._tooltip = tooltip;
}

function hideTooltip(e) {
    if (e.target._tooltip) {
        e.target._tooltip.remove();
        delete e.target._tooltip;
    }
}

// ===== УВЕДОМЛЕНИЯ =====
function initializeAlerts() {
    // Автоматическое скрытие уведомлений
    document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
        const delay = parseInt(alert.getAttribute('data-auto-dismiss')) || 5000;
        setTimeout(() => {
            dismissAlert(alert);
        }, delay);
    });

    // Кнопки закрытия уведомлений
    document.querySelectorAll('.alert .btn-close').forEach(btn => {
        btn.addEventListener('click', function () {
            dismissAlert(this.closest('.alert'));
        });
    });
}

function dismissAlert(alert) {
    alert.style.opacity = '0';
    alert.style.transform = 'translateY(-20px)';
    setTimeout(() => {
        alert.remove();
    }, 300);
}

function showAlert(message, type = 'info', autoDismiss = true) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span>${message}</span>
            <button class="btn-close" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>
    `;

    if (autoDismiss) {
        alert.setAttribute('data-auto-dismiss', '5000');
    }

    const container = document.querySelector('.alerts-container') || document.body;
    container.insertBefore(alert, container.firstChild);

    if (autoDismiss) {
        setTimeout(() => dismissAlert(alert), 5000);
    }

    return alert;
}

// ===== ФОРМЫ =====
function initializeForms() {
    // Валидация форм в реальном времени
    document.querySelectorAll('form[data-validate]').forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearFieldError);
        });
    });

    // Подтверждение удаления
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function (e) {
            const message = this.getAttribute('data-confirm') || 'Вы уверены?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // AJAX отправка форм
    document.querySelectorAll('form[data-ajax]').forEach(form => {
        form.addEventListener('submit', handleAjaxSubmit);
    });
}

function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    const rules = field.getAttribute('data-validate');

    if (!rules) return;

    let isValid = true;
    let errorMessage = '';

    // Проверка обязательности
    if (rules.includes('required') && !value) {
        isValid = false;
        errorMessage = 'Это поле обязательно для заполнения';
    }

    // Проверка email
    if (rules.includes('email') && value && !isValidEmail(value)) {
        isValid = false;
        errorMessage = 'Введите корректный email адрес';
    }

    // Проверка минимальной длины
    const minLength = rules.match(/min:(\d+)/);
    if (minLength && value.length < parseInt(minLength[1])) {
        isValid = false;
        errorMessage = `Минимальная длина: ${minLength[1]} символов`;
    }

    // Проверка максимальной длины
    const maxLength = rules.match(/max:(\d+)/);
    if (maxLength && value.length > parseInt(maxLength[1])) {
        isValid = false;
        errorMessage = `Максимальная длина: ${maxLength[1]} символов`;
    }

    setFieldValidation(field, isValid, errorMessage);
}

function clearFieldError(e) {
    const field = e.target;
    setFieldValidation(field, true, '');
}

function setFieldValidation(field, isValid, errorMessage) {
    const feedback = field.parentNode.querySelector('.invalid-feedback');

    if (isValid) {
        field.classList.remove('is-invalid');
        if (feedback) feedback.remove();
    } else {
        field.classList.add('is-invalid');
        if (feedback) {
            feedback.textContent = errorMessage;
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errorMessage;
            field.parentNode.appendChild(errorDiv);
        }
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

async function handleAjaxSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn.textContent;

    // Показываем загрузку
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loader"></span> Отправка...';

    try {
        const response = await fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (response.ok) {
            showAlert(data.message || 'Операция выполнена успешно', 'success');
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } else {
            showAlert(data.message || 'Произошла ошибка', 'danger');
        }
    } catch (error) {
        showAlert('Ошибка соединения с сервером', 'danger');
    } finally {
        // Восстанавливаем кнопку
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

// ===== АДМИНИСТРАТОРСКАЯ ПАНЕЛЬ =====
function initializeAdmin() {
    // Сворачивание сайдбара
    const sidebarToggle = document.querySelector('.admin-sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
    }

    // Мобильное меню админки
    const mobileToggle = document.querySelector('.admin-mobile-toggle');
    if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // Выпадающее меню пользователя
    const userBtn = document.querySelector('.admin-user-btn');
    const userDropdown = document.querySelector('.admin-user-dropdown');

    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        // Закрытие при клике вне меню
        document.addEventListener('click', function () {
            userDropdown.classList.remove('active');
        });
    }

    // Инициализация графиков
    initializeCharts();

    // Инициализация таблиц
    initializeTables();
}

function initializeCharts() {
    // Инициализация Chart.js графиков
    document.querySelectorAll('.admin-chart').forEach(canvas => {
        const ctx = canvas.getContext('2d');
        const data = JSON.parse(canvas.getAttribute('data-chart-data'));
        const type = canvas.getAttribute('data-chart-type') || 'line';

        new Chart(ctx, {
            type: type,
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
}

function initializeTables() {
    // Сортировка таблиц
    document.querySelectorAll('.admin-table th[data-sort]').forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function () {
            sortTable(this);
        });
    });

    // Поиск в таблицах
    document.querySelectorAll('.admin-table-search').forEach(searchInput => {
        searchInput.addEventListener('input', function () {
            filterTable(this);
        });
    });
}

function sortTable(header) {
    const table = header.closest('table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const column = header.cellIndex;
    const isAscending = header.classList.contains('sort-asc');

    // Сбрасываем сортировку для всех заголовков
    table.querySelectorAll('th').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });

    // Устанавливаем направление сортировки
    header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');

    // Сортируем строки
    rows.sort((a, b) => {
        const aText = a.cells[column].textContent.trim();
        const bText = b.cells[column].textContent.trim();

        if (isAscending) {
            return bText.localeCompare(aText);
        } else {
            return aText.localeCompare(bText);
        }
    });

    // Перестраиваем таблицу
    rows.forEach(row => tbody.appendChild(row));
}

function filterTable(searchInput) {
    const table = searchInput.closest('.admin-table-container').querySelector('table');
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    const searchTerm = searchInput.value.toLowerCase();

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ===== УТИЛИТЫ =====
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ===== ЭКСПОРТ ФУНКЦИЙ =====
window.App = {
    showAlert,
    openModal,
    closeModal,
    debounce,
    throttle
};