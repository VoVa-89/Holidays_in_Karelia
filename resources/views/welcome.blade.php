@extends('layouts.app')

@section('title', 'Отдых в Карелии - Главная')
@section('description', 'Туристический блог и гид по Карелии. Откройте для себя удивительные места, маршруты и достопримечательности этого прекрасного края.')

@section('content')
<div class="container my-5">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-mountain me-3"></i>
                        Добро пожаловать в Карелию!
                    </h1>
                    <p class="lead mb-4">
                        Откройте для себя удивительные места, маршруты и достопримечательности 
                        одного из самых красивых регионов России
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('posts.index') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-map-marked-alt me-2"></i>Исследовать места
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-list me-2"></i>Категории
                        </a>
                    @auth
                            <a href="{{ route('posts.create') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-plus me-2"></i>Добавить пост
                            </a>
                    @else
                            <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Присоединиться
                            </a>
                    @endauth
                </div>
                </div>
            </div>
        </div>
                                </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-5">Почему выбирают нас?</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-map-marked-alt fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Уникальные маршруты</h5>
                    <p class="card-text">
                        Откройте для себя скрытые жемчужины Карелии с нашими 
                        проверенными маршрутами и рекомендациями
                                </p>
                            </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-primary"></i>
                                </div>
                    <h5 class="card-title">Сообщество путешественников</h5>
                    <p class="card-text">
                        Присоединяйтесь к сообществу любителей Карелии, 
                        делитесь опытом и открывайте новые места
                                </p>
                            </div>
                                </div>
                            </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-star fa-3x text-primary"></i>
                                </div>
                    <h5 class="card-title">Проверенные места</h5>
                    <p class="card-text">
                        Все места проверены нашими экспертами и оценены 
                        сообществом путешественников
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

    <!-- Quick Stats -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="h2 text-primary mb-1">150+</div>
                            <div class="text-muted">Мест для посещения</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="h2 text-primary mb-1">50+</div>
                            <div class="text-muted">Маршрутов</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="h2 text-primary mb-1">1000+</div>
                            <div class="text-muted">Отзывов</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="h2 text-primary mb-1">500+</div>
                            <div class="text-muted">Участников</div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body text-center py-4">
                    <h3 class="card-title mb-3">Готовы начать путешествие?</h3>
                    <p class="card-text mb-4">
                        Присоединяйтесь к нашему сообществу и откройте для себя 
                        всю красоту Карелии
                    </p>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Войти
                        </a>
                    @else
                        <a href="{{ route('posts.create') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-plus me-2"></i>Добавить место
                        </a>
                        <a href="{{ route('posts.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Найти места
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, rgba(44, 85, 48, 0.9) 0%, rgba(34, 139, 34, 0.9) 100%);
        background-size: cover;
        background-position: center;
    }
    
    .feature-card {
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
    }
</style>
@endpush