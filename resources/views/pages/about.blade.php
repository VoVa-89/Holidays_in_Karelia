@extends('layouts.app')

@section('title', 'О нас — Отдых в Карелии')
@section('description', 'Забудьте о скучных путеводителях! Наш сайт — это живой гид по самым удивительным местам Карелии. Откройте для себя скрытые жемчужины, спланируйте маршрут мечты и получите незабываемые впечатления.')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Заголовок -->
                <h1 class="h2 mb-4 text-center">
                    <i class="fas fa-mountain text-primary me-2"></i>
                    Карелия: ваш идеальный отпуск начинается здесь
                </h1>

                <!-- Картинка -->
                <div class="text-center mb-4">
                    <img src="{{ asset('images/og-image.jpg') }}" 
                         alt="Отдых в Карелии" 
                         class="img-fluid rounded-4 shadow-sm"
                         style="max-height: 400px; object-fit: cover; width: 100%;">
                </div>

                <!-- Описание -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <p class="lead mb-0">
                            Забудьте о скучных путеводителях! Наш сайт — это живой гид по самым удивительным местам Карелии. Откройте для себя скрытые жемчужины, спланируйте маршрут мечты и получите незабываемые впечатления.
                        </p>
                    </div>
                </div>

                <!-- Выгоды -->
                <div class="card border-primary shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0">
                            <i class="fas fa-star me-2"></i>Что вы получите
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>
                                    <strong>Находите уникальные места для отдыха</strong>
                                    <p class="text-muted small mb-0">Откройте для себя скрытые жемчужины, которых нет в стандартных путеводителях.</p>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>
                                    <strong>Создавайте маршруты под свои интересы</strong>
                                    <p class="text-muted small mb-0">Персонализируйте свое путешествие: от сплавов по рекам до отдыха на турбазах.</p>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>
                                    <strong>Читайте честные отзывы и смотрите реальные фото</strong>
                                    <p class="text-muted small mb-0">Получайте достоверную информацию от других туристов, которые уже побывали в этих местах.</p>
                                </div>
                            </li>
                            <li class="mb-0 d-flex align-items-start">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>
                                    <strong>Экономьте время — вся информация собрана в одном месте</strong>
                                    <p class="text-muted small mb-0">Не нужно искать информацию в разных источниках. Все о достопримечательностях, жилье и ценах — здесь.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Призыв к действию -->
                <div class="card border-success shadow-sm mb-4">
                    <div class="card-body p-4 text-center">
                        <h3 class="h5 mb-3">
                            <i class="fas fa-map-marked-alt text-success me-2"></i>
                            Найдите идеальное место для отдыха и начните планировать свое путешествие!
                        </h3>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <a href="{{ route('posts.index') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-list me-2"></i>Все посты
                            </a>
                            <a href="{{ route('posts.index', ['category' => 'dostoprimechatelnosti']) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-landmark me-2"></i>Достопримечательности
                            </a>
                            <a href="{{ route('posts.index', ['category' => 'mesta-otdykha']) }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-campground me-2"></i>Места отдыха
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="text-center text-muted small">
                    <p class="mb-0">
                        Сделано с <i class="fas fa-heart text-danger me-1"></i> для любителей Карелии
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

