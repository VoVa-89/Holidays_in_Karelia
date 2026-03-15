<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Отдых в Карелии')</title>
    <meta name="description" content="@yield('description', 'Забудьте о скучных путеводителях! Наш сайт — это живой гид по самым удивительным местам Карелии. Откройте для себя скрытые жемчужины, спланируйте маршрут мечты и получите незабываемые впечатления.')">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og:title', 'Карелия: ваш идеальный отпуск начинается здесь — Отдых в Карелии')">
    <meta property="og:description" content="@yield('og:description', 'Забудьте о скучных путеводителях! Наш сайт — это живой гид по самым удивительным местам Карелии. Откройте для себя скрытые жемчужины, спланируйте маршрут мечты и получите незабываемые впечатления.')">
    <meta property="og:image" content="@yield('og:image', asset('images/og-image.jpg'))">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:site_name" content="Отдых в Карелии">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('og:title', 'Карелия: ваш идеальный отпуск начинается здесь — Отдых в Карелии')">
    <meta name="twitter:description" content="@yield('og:description', 'Забудьте о скучных путеводителях! Наш сайт — это живой гид по самым удивительным местам Карелии. Откройте для себя скрытые жемчужины, спланируйте маршрут мечты и получите незабываемые впечатления.')">
    <meta name="twitter:image" content="@yield('og:image', asset('images/og-image.jpg'))">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/components.css') }}" rel="stylesheet">
    
    <!-- Admin CSS (только для админ-панели) -->
    @if(request()->is('admin*'))
        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @endif
    
    <!-- Additional CSS -->
    @stack('styles')
    
    <!-- Yandex Maps Configuration -->
    <meta name="yandex-maps-key" content="{{ config('services.yandex.maps_key') }}">
</head>
<body>
    <!-- Navigation -->
    @include('partials.nav')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <div class="container">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show m-0" role="alert">
            <div class="container">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show m-0" role="alert">
            <div class="container">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-mountain me-2"></i>Отдых в Карелии
                    </h5>
                    <p class="mb-3">
                        Ваш надежный гид по красотам Карелии. Откройте для себя удивительные места, 
                        маршруты и достопримечательности этого прекрасного края.
                    </p>
					{{-- Социальные иконки скрыты по требованию — блок удален --}}
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Навигация</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-light text-decoration-none">Главная</a></li>
                        <li><a href="{{ route('about') }}" class="text-light text-decoration-none">О нас</a></li>
                        <li><a href="{{ route('guidelines') }}" class="text-light text-decoration-none">Правила публикации</a></li>
                        @auth
                            <li><a href="{{ route('posts.create') }}" class="text-light text-decoration-none">Добавить пост</a></li>
                            @if(Auth::user()->isAdmin())
                                <li><a href="{{ route('admin.dashboard') }}" class="text-light text-decoration-none">Админ-панель</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Контакты</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i>krupenkin.vov@yandex.ru</li>
                        <li><i class="fas fa-phone me-2"></i>+7 (921) 222-30-98</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>Петрозаводск, Карелия</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; {{ date('Y') }} Отдых в Карелии. Все права защищены.<br>
                        <a href="{{ route('privacy.policy') }}" class="text-light text-decoration-none">
                            Политика конфиденциальности
                        </a>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-white">
                        Сделано с <i class="fas fa-heart text-danger"></i> для любителей Карелии
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js (только для админ-панели) -->
    @if(request()->is('admin*'))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif
    
    <!-- Yandex Maps Global Configuration -->
    <script>
        window.yandexMapsKey = '{{ config("services.yandex.maps_key") }}';
        console.log('🔑 Yandex Maps API Key загружен:', window.yandexMapsKey ? 'найден' : 'не найден');
    </script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/yandex-maps.js') }}"></script>
    
    <!-- Additional JS -->
    @stack('scripts')

    <!-- Cookie Banner -->
    <div id="cookie-banner" class="cookie-banner" style="display:none;">
        <div class="cookie-banner__content">
            <span class="cookie-banner__text">
                Мы используем файлы cookies и обезличенные данные об использовании сайта для улучшения работы сервиса.
                Продолжая пользоваться сайтом, вы соглашаетесь с
                <a href="{{ route('privacy.policy') }}" class="cookie-banner__link">Политикой конфиденциальности</a>.
            </span>
            <button type="button" id="cookie-banner-accept" class="cookie-banner__button">
                Понятно
            </button>
        </div>
    </div>

    <script>
        (function () {
            var bannerId = 'cookie-banner';
            var acceptId = 'cookie-banner-accept';
            var storageKey = 'hk_cookie_banner_accepted';

            function hideBanner() {
                var banner = document.getElementById(bannerId);
                if (banner) {
                    banner.style.display = 'none';
                }
            }

            function showBanner() {
                var banner = document.getElementById(bannerId);
                if (banner) {
                    banner.style.display = 'block';
                }
            }

            function initCookieBanner() {
                try {
                    if (localStorage.getItem(storageKey) === '1') {
                        return;
                    }
                } catch (e) {
                    // если localStorage недоступен — просто показываем баннер
                }

                showBanner();

                var btn = document.getElementById(acceptId);
                if (btn) {
                    btn.addEventListener('click', function () {
                        try {
                            localStorage.setItem(storageKey, '1');
                        } catch (e) {
                            // игнорируем ошибки
                        }
                        hideBanner();
                    });
                }
            }

            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                initCookieBanner();
            } else {
                document.addEventListener('DOMContentLoaded', initCookieBanner);
            }
        })();
    </script>

    <style>
        .cookie-banner {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.88);
            color: #fff;
            padding: 12px 16px;
            font-size: 14px;
        }

        .cookie-banner__content {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .cookie-banner__text {
            line-height: 1.4;
        }

        .cookie-banner__link {
            color: #4ea3ff;
            text-decoration: underline;
        }

        .cookie-banner__link:hover {
            color: #6bb3ff;
        }

        .cookie-banner__button {
            background: #4ea3ff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 14px;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap;
            transition: background-color 0.2s;
        }

        .cookie-banner__button:hover {
            background: #2f7ed8;
        }

        @media (max-width: 768px) {
            .cookie-banner__content {
                flex-direction: column;
                align-items: flex-start;
            }

            .cookie-banner__button {
                width: 100%;
                margin-top: 8px;
            }
        }
    </style>
</body>
</html>