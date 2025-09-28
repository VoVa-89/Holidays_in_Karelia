@extends('layouts.app')

@section('title', '404 - Страница не найдена | Отдых в Карелии')
@section('description', 'К сожалению, запрашиваемая страница не найдена. Но не расстраивайтесь - у нас есть много интересных мест в Карелии!')

@section('content')
<div class="error-404-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Основной контент 404 -->
                <div class="error-404-content text-center">
                    <!-- Иконка и заголовок -->
                    <div class="error-404-header mb-5">
                        <div class="error-404-icon mb-4">
                            <i class="fas fa-mountain-sun"></i>
                        </div>
                        <h1 class="error-404-title">404</h1>
                        <h2 class="error-404-subtitle">Страница не найдена</h2>
                        <p class="error-404-description">
                            Похоже, вы заблудились в карельских лесах! 🌲<br>
                            Не волнуйтесь - мы поможем вам найти дорогу к интересным местам.
                        </p>
                    </div>

                    <!-- Тематическое изображение -->
                    <div class="error-404-image mb-5">
                        <div class="karelia-landscape">
                            <div class="landscape-layer mountains">
                                <div class="mountain mountain-1"></div>
                                <div class="mountain mountain-2"></div>
                                <div class="mountain mountain-3"></div>
                            </div>
                            <div class="landscape-layer forest">
                                <div class="tree tree-1"></div>
                                <div class="tree tree-2"></div>
                                <div class="tree tree-3"></div>
                                <div class="tree tree-4"></div>
                                <div class="tree tree-5"></div>
                            </div>
                            <div class="landscape-layer lake">
                                <div class="wave wave-1"></div>
                                <div class="wave wave-2"></div>
                                <div class="wave wave-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Поисковая форма -->
                    <div class="error-404-search mb-5">
                        <h3 class="search-title mb-3">
                            <i class="fas fa-search me-2"></i>Найти интересные места
                        </h3>
                        <form action="{{ route('posts.index') }}" method="GET" class="search-form">
                            <div class="input-group input-group-lg">
                                <input type="text" 
                                       name="search" 
                                       class="form-control search-input" 
                                       placeholder="Поиск по местам, маршрутам, достопримечательностям..."
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary search-btn">
                                    <i class="fas fa-search me-2"></i>Найти
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Полезные ссылки -->
                    <div class="error-404-links mb-5">
                        <h3 class="links-title mb-4">
                            <i class="fas fa-compass me-2"></i>Куда отправиться дальше?
                        </h3>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <a href="{{ route('home') }}" class="error-link-card">
                                    <div class="link-icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <h4>Главная страница</h4>
                                    <p>Вернуться на главную и начать путешествие</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('posts.index') }}" class="error-link-card">
                                    <div class="link-icon">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    <h4>Все места</h4>
                                    <p>Исследовать все интересные места Карелии</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('categories.index') }}" class="error-link-card">
                                    <div class="link-icon">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <h4>Категории</h4>
                                    <p>Найти места по категориям</p>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Популярные категории -->
                    <div class="error-404-categories mb-5">
                        <h3 class="categories-title mb-4">
                            <i class="fas fa-tags me-2"></i>Популярные категории
                        </h3>
                        <div class="categories-grid">
                            @foreach(\App\Models\Category::withCount('posts')->orderBy('posts_count', 'desc')->limit(6)->get() as $category)
                                <a href="{{ route('categories.show', $category->slug) }}" class="category-badge">
                                    <i class="fas fa-tag me-2"></i>{{ $category->name }}
                                    <span class="badge-count">{{ $category->posts_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Кнопка "Вернуться на главную" -->
                    <div class="error-404-actions">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-home me-2"></i>На главную
                        </a>
                        <a href="{{ route('posts.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-compass me-2"></i>Исследовать места
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/404.css') }}" rel="stylesheet">
<style>
/* ===== ОСНОВНЫЕ СТИЛИ 404 ===== */
.error-404-container {
    min-height: 80vh;
    background: linear-gradient(135deg, #F7F9FB 0%, #E8F4F8 100%);
    padding: 4rem 0;
    position: relative;
    overflow: hidden;
}

.error-404-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="forest" patternUnits="userSpaceOnUse" width="20" height="20"><path d="M10 0 L15 10 L5 10 Z" fill="%23E8F4F8" opacity="0.3"/></pattern></defs><rect width="100" height="100" fill="url(%23forest)"/></svg>');
    opacity: 0.1;
    pointer-events: none;
}

.error-404-content {
    position: relative;
    z-index: 2;
}

/* ===== ЗАГОЛОВОК И ОПИСАНИЕ ===== */
.error-404-header {
    margin-bottom: 3rem;
}

.error-404-icon {
    font-size: 4rem;
    color: var(--k-water);
    animation: float 3s ease-in-out infinite;
}

.error-404-title {
    font-size: 6rem;
    font-weight: 900;
    color: var(--k-forest);
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    animation: pulse 2s ease-in-out infinite;
}

.error-404-subtitle {
    font-size: 2rem;
    font-weight: 600;
    color: var(--k-text);
    margin: 1rem 0;
}

.error-404-description {
    font-size: 1.2rem;
    color: var(--k-text-light);
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

/* ===== ТЕМАТИЧЕСКОЕ ИЗОБРАЖЕНИЕ ===== */
.error-404-image {
    margin: 3rem 0;
}

.karelia-landscape {
    position: relative;
    width: 100%;
    height: 200px;
    max-width: 600px;
    margin: 0 auto;
    overflow: hidden;
    border-radius: var(--k-radius-lg);
    box-shadow: var(--k-shadow-md);
}

.landscape-layer {
    position: absolute;
    width: 100%;
    height: 100%;
}

/* Горы */
.mountains {
    background: linear-gradient(135deg, var(--k-forest) 0%, var(--k-forest-2) 100%);
    z-index: 3;
}

.mountain {
    position: absolute;
    bottom: 0;
    background: var(--k-stone);
    border-radius: 50% 50% 0 0;
}

.mountain-1 {
    width: 120px;
    height: 80px;
    left: 10%;
    animation: mountainGlow 4s ease-in-out infinite;
}

.mountain-2 {
    width: 100px;
    height: 60px;
    left: 40%;
    animation: mountainGlow 4s ease-in-out infinite 1s;
}

.mountain-3 {
    width: 80px;
    height: 50px;
    left: 70%;
    animation: mountainGlow 4s ease-in-out infinite 2s;
}

/* Лес */
.forest {
    z-index: 2;
    bottom: 0;
    height: 60px;
}

.tree {
    position: absolute;
    bottom: 0;
    background: var(--k-forest);
    border-radius: 50% 50% 0 0;
    animation: treeSway 3s ease-in-out infinite;
}

.tree-1 { width: 20px; height: 40px; left: 15%; animation-delay: 0s; }
.tree-2 { width: 25px; height: 45px; left: 25%; animation-delay: 0.5s; }
.tree-3 { width: 18px; height: 35px; left: 35%; animation-delay: 1s; }
.tree-4 { width: 22px; height: 42px; left: 55%; animation-delay: 1.5s; }
.tree-5 { width: 20px; height: 38px; left: 75%; animation-delay: 2s; }

/* Озеро */
.lake {
    background: linear-gradient(135deg, var(--k-water) 0%, var(--k-water-2) 100%);
    height: 30px;
    bottom: 0;
    z-index: 1;
}

.wave {
    position: absolute;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: wave 2s ease-in-out infinite;
}

.wave-1 { width: 30px; height: 8px; left: 20%; top: 10px; animation-delay: 0s; }
.wave-2 { width: 25px; height: 6px; left: 50%; top: 8px; animation-delay: 0.7s; }
.wave-3 { width: 35px; height: 10px; left: 75%; top: 12px; animation-delay: 1.4s; }

/* ===== ПОИСКОВАЯ ФОРМА ===== */
.error-404-search {
    background: var(--bg-white);
    border-radius: var(--k-radius-lg);
    padding: 2rem;
    box-shadow: var(--k-shadow-md);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.search-title {
    color: var(--k-forest);
    font-weight: 600;
    font-size: 1.5rem;
}

.search-form .input-group {
    max-width: 600px;
    margin: 0 auto;
}

.search-input {
    border: 2px solid var(--border-color);
    border-radius: var(--k-radius) 0 0 var(--k-radius);
    font-size: 1.1rem;
    padding: 1rem 1.5rem;
    transition: var(--transition);
}

.search-input:focus {
    border-color: var(--k-water);
    box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
    outline: none;
}

.search-btn {
    border-radius: 0 var(--k-radius) var(--k-radius) 0;
    padding: 1rem 2rem;
    font-weight: 600;
    background: var(--k-gradient-water);
    border: none;
    transition: var(--transition);
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--k-shadow-md);
}

/* ===== ПОЛЕЗНЫЕ ССЫЛКИ ===== */
.error-404-links {
    margin: 3rem 0;
}

.links-title {
    color: var(--k-forest);
    font-weight: 600;
    font-size: 1.5rem;
}

.error-link-card {
    display: block;
    background: var(--bg-white);
    border-radius: var(--k-radius-lg);
    padding: 2rem;
    text-decoration: none;
    color: inherit;
    box-shadow: var(--k-shadow-sm);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: var(--transition);
    height: 100%;
}

.error-link-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--k-shadow-lg);
    color: inherit;
    text-decoration: none;
}

.error-link-card .link-icon {
    width: 60px;
    height: 60px;
    background: var(--k-gradient-water);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
    transition: var(--transition);
}

.error-link-card:hover .link-icon {
    transform: scale(1.1);
    box-shadow: var(--k-shadow-md);
}

.error-link-card h4 {
    color: var(--k-forest);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.2rem;
}

.error-link-card p {
    color: var(--k-text-light);
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* ===== КАТЕГОРИИ ===== */
.error-404-categories {
    margin: 3rem 0;
}

.categories-title {
    color: var(--k-forest);
    font-weight: 600;
    font-size: 1.5rem;
}

.categories-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    background: var(--bg-white);
    color: var(--k-text);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
    box-shadow: var(--k-shadow-sm);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: var(--transition);
}

.category-badge:hover {
    background: var(--k-water);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--k-shadow-md);
    text-decoration: none;
}

.badge-count {
    background: var(--k-gold);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

.category-badge:hover .badge-count {
    background: rgba(255, 255, 255, 0.2);
}

/* ===== КНОПКИ ДЕЙСТВИЙ ===== */
.error-404-actions {
    margin-top: 3rem;
}

.error-404-actions .btn {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: var(--k-radius);
    transition: var(--transition);
}

.error-404-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--k-shadow-md);
}

/* ===== АНИМАЦИИ ===== */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes mountainGlow {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

@keyframes treeSway {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(1deg); }
    75% { transform: rotate(-1deg); }
}

@keyframes wave {
    0%, 100% { transform: translateX(0) scale(1); opacity: 0.3; }
    50% { transform: translateX(10px) scale(1.1); opacity: 0.6; }
}

/* ===== АДАПТИВНОСТЬ ===== */
@media (max-width: 768px) {
    .error-404-container {
        padding: 2rem 0;
    }
    
    .error-404-title {
        font-size: 4rem;
    }
    
    .error-404-subtitle {
        font-size: 1.5rem;
    }
    
    .error-404-description {
        font-size: 1rem;
    }
    
    .karelia-landscape {
        height: 150px;
    }
    
    .error-404-search {
        padding: 1.5rem;
    }
    
    .search-form .input-group {
        flex-direction: column;
    }
    
    .search-input {
        border-radius: var(--k-radius);
        margin-bottom: 1rem;
    }
    
    .search-btn {
        border-radius: var(--k-radius);
    }
    
    .error-link-card {
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .categories-grid {
        flex-direction: column;
        align-items: center;
    }
    
    .category-badge {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .error-404-title {
        font-size: 3rem;
    }
    
    .error-404-subtitle {
        font-size: 1.25rem;
    }
    
    .error-404-icon {
        font-size: 3rem;
    }
    
    .karelia-landscape {
        height: 120px;
    }
    
    .error-404-search {
        padding: 1rem;
    }
    
    .error-link-card {
        padding: 1rem;
    }
    
    .error-404-actions .btn {
        width: 100%;
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Анимация появления элементов при скролле
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, observerOptions);

    // Наблюдаем за элементами для анимации
    document.querySelectorAll('.error-link-card, .category-badge, .error-404-search').forEach(el => {
        el.classList.add('scroll-reveal');
        observer.observe(el);
    });

    // Улучшенная анимация для ландшафта
    const landscape = document.querySelector('.karelia-landscape');
    if (landscape) {
        landscape.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.3s ease';
        });

        landscape.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }

    // Анимация для кнопок поиска
    const searchBtn = document.querySelector('.search-btn');
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            // Добавляем эффект пульсации
            this.style.animation = 'pulse 0.6s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 600);
        });
    }

    // Улучшенная анимация для карточек ссылок
    document.querySelectorAll('.error-link-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Анимация для бейджей категорий
    document.querySelectorAll('.category-badge').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });

        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Плавная прокрутка для якорных ссылок
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Добавляем эффект печатания для заголовка
    const title = document.querySelector('.error-404-title');
    if (title) {
        const originalText = title.textContent;
        title.textContent = '';
        let i = 0;
        
        function typeWriter() {
            if (i < originalText.length) {
                title.textContent += originalText.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        }
        
        setTimeout(typeWriter, 500);
    }

    // Анимация для иконки
    const icon = document.querySelector('.error-404-icon i');
    if (icon) {
        setInterval(() => {
            icon.style.transform = 'rotate(5deg)';
            setTimeout(() => {
                icon.style.transform = 'rotate(-5deg)';
            }, 200);
            setTimeout(() => {
                icon.style.transform = 'rotate(0deg)';
            }, 400);
        }, 3000);
    }
});
</script>
@endpush
