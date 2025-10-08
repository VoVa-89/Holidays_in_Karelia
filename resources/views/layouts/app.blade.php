<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Отдых в Карелии')</title>
    <meta name="description" content="@yield('description', 'Туристический блог и гид по Карелии. Статьи, маршруты, достопримечательности и полезная информация для путешественников.')">

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
                        <li><a href="{{ route('categories.index') }}" class="text-light text-decoration-none">Категории</a></li>
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
                    <p class="mb-0">&copy; {{ date('Y') }} Отдых в Карелии. Все права защищены.</p>
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
</body>
</html>