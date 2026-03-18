@extends('layouts.app')

@section('title', 'Отдых в Карелии — Главная')
@section('description', 'Забудьте о скучных путеводителях! Наш сайт — это живой гид по самым удивительным местам Карелии. Откройте для себя скрытые жемчужины, спланируйте маршрут мечты и получите незабываемые впечатления.')

@section('content')

	<div class="container my-5">
		<!-- Hero Section -->
		<div class="hero-section rounded-4 p-5 mb-5" style="
			background:
				linear-gradient(to right, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.25) 48%, rgba(0, 0, 0, 0) 72%),
				linear-gradient(rgba(255, 255, 255, 0.08), rgba(0, 0, 0, 0.25)),
				url('{{ asset('images/hero-background.jpg') }}') center/cover;
			min-height: 500px;
		">
			<div class="row align-items-center h-100">
				<div class="col-lg-7 mb-4 mb-lg-0">
					<h1 class="hero-title fw-bold mb-3 text-white" style="text-shadow: 0 3px 12px rgba(0, 0, 0, 0.65), 0 0 28px rgba(0, 0, 0, 0.35);">
						Карелия: ваш идеальный отпуск начинается здесь
					</h1>
					<p class="lead text-white mb-4" style="text-shadow: 0 2px 8px rgba(0, 0, 0, 0.65); font-weight: 500;">
						Забудьте о скучных путеводителях! Наш сайт — это живой гид по самым удивительным местам Карелии. Откройте для себя скрытые жемчужины, спланируйте маршрут мечты и получите незабываемые впечатления.
					</p>
					<div class="d-flex flex-wrap gap-2 mb-3">
						<a href="{{ route('posts.index', ['category' => 'dostoprimechatelnosti']) }}" class="btn btn-success">
							<i class="fas fa-landmark me-2"></i>Достопримечательности
						</a>
						<a href="{{ route('posts.index', ['category' => 'mesta-otdykha']) }}" class="btn btn-primary">
							<i class="fas fa-campground me-2"></i>Места отдыха
						</a>
						<a href="{{ route('posts.index') }}" class="btn btn-yellow">
							<i class="fas fa-list me-2"></i>Все посты
						</a>
					</div>
					
					<!-- Описания категорий -->
					<div class="row g-3">
						<div class="col-md-6">
						<div class="card border-success h-100" style="
							background: rgba(255, 255, 255, 0.5) !important; 
							backdrop-filter: blur(30px) !important;
							-webkit-backdrop-filter: blur(30px) !important;
							border: 2px solid rgba(255, 255, 255, 0.6) !important;
							box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
						">
							<div class="card-body">
								<h6 class="card-title text-success mb-2 fw-bold">
									<i class="fas fa-landmark me-2"></i>Достопримечательности
								</h6>
							<p class="card-text mb-0" style="color: #333; font-size: 0.875rem; line-height: 1.55;">
								Уникальные природные и рукотворные объекты, представляющие культурную, историческую или природную ценность. Места, которые стоит посетить для знакомства с наследием и красотой Карелии.
							</p>
							</div>
						</div>
						</div>
						<div class="col-md-6">
						<div class="card border-primary h-100" style="
							background: rgba(255, 255, 255, 0.5) !important; 
							backdrop-filter: blur(30px) !important;
							-webkit-backdrop-filter: blur(30px) !important;
							border: 2px solid rgba(255, 255, 255, 0.6) !important;
							box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
						">
							<div class="card-body">
								<h6 class="card-title text-primary mb-2 fw-bold">
									<i class="fas fa-campground me-2"></i>Места отдыха
								</h6>
							<p class="card-text mb-0" style="color: #333; font-size: 0.875rem; line-height: 1.55;">
								Объекты инфраструктуры и локации, созданные для комфортного проведения досуга. Места, где можно остановиться, отдохнуть и получить услуги для полноценного отдыха в Карелии.
							</p>
							</div>
						</div>
						</div>
					</div>
				</div>
				<div class="col-lg-5">
					<div class="card shadow-lg h-100" style="
						background: rgba(255, 255, 255, 0.5) !important; 
						backdrop-filter: blur(30px) !important;
						-webkit-backdrop-filter: blur(30px) !important;
						border: 2px solid rgba(255, 255, 255, 0.6) !important;
						box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
					">
						<div class="card-body">
							<h5 class="card-title mb-3"><i class="fas fa-bullhorn me-2"></i>Добро пожаловать!</h5>
							@guest
								<p class="mb-3">Создайте аккаунт, чтобы добавлять места, оставлять комментарии и ставить оценки.</p>
								<div class="d-flex flex-wrap gap-2">
									<a href="{{ route('register') }}" class="btn btn-success me-1">
										<i class="fas fa-user-plus me-2"></i>Регистрация
									</a>
									<a href="{{ route('login') }}" class="btn btn-primary">
										<i class="fas fa-sign-in-alt me-2"></i>Войти
									</a>
								</div>
							@else
								<p class="mb-3">Вы вошли как <strong>{{ Auth::user()->name }}</strong>. Делитесь любимыми местами Карелии!</p>
								<a href="{{ route('posts.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Добавить пост</a>
								@if(Auth::user()->isAdmin())
									<a href="{{ route('admin.moderation') }}" class="btn btn-warning ms-2"><i class="fas fa-gavel me-2"></i>Модерация</a>
								@endif
							@endguest
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Топ постов по категориям -->
		<div class="row g-4 mb-5">
			<div class="col-12 col-xl-6">
				<x-top-posts-list
					title="Популярные достопримечательности"
					:posts="$topAttractions"
					icon="fas fa-landmark"
					colorClass="success"
					categorySlug="dostoprimechatelnosti"
				/>
			</div>
			<div class="col-12 col-xl-6">
				<x-top-posts-list
					title="Лучшие места для отдыха"
					:posts="$topRestPlaces"
					icon="fas fa-campground"
					colorClass="primary"
					categorySlug="mesta-otdykha"
				/>
			</div>
		</div>

		<!-- Компонент карты -->
		<x-main-map :posts="$mapPosts" />

		<!-- CTA Section -->
		<div class="row mb-4">
			<div class="col-12">
				<div class="card border-primary">
					<div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
						<div class="mb-3 mb-lg-0">
							<h3 class="mb-2">Присоединяйтесь к сообществу путешественников Карелии</h3>
							<p class="text-muted mb-0">Делитесь своими впечатлениями, добавляйте новые места и помогайте другим планировать путешествия.</p>
						</div>
						<div>
							@guest
								<a href="{{ route('register') }}" class="btn btn-primary me-2"><i class="fas fa-user-plus me-2"></i>Зарегистрироваться</a>
								<a href="{{ route('login') }}" class="btn btn-outline-primary"><i class="fas fa-sign-in-alt me-2"></i>Войти</a>
							@else
								<a href="{{ route('posts.create') }}" class="btn btn-success me-2"><i class="fas fa-plus me-2"></i>Создать пост</a>
								<a href="{{ route('posts.index') }}" class="btn btn-outline-secondary"><i class="fas fa-list me-2"></i>Смотреть посты</a>
							@endguest
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@push('schema')
@php
$websiteSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'WebSite',
    'name'       => 'Отдых в Карелии',
    'url'        => url('/'),
    'inLanguage' => 'ru-RU',
    'description' => 'Живой гид по самым удивительным местам Карелии. Достопримечательности, места отдыха, маршруты.',
    'publisher'  => [
        '@type' => 'Organization',
        'name'  => 'Отдых в Карелии',
        'url'   => url('/'),
        'logo'  => [
            '@type' => 'ImageObject',
            'url'   => asset('images/og-image.jpg'),
        ],
        'contactPoint' => [
            '@type'       => 'ContactPoint',
            'telephone'   => '+7-921-222-30-98',
            'email'       => 'krupenkin.vov@yandex.ru',
            'contactType' => 'customer service',
            'areaServed'  => 'RU',
        ],
        'address' => [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'Петрозаводск',
            'addressRegion'   => 'Республика Карелия',
            'addressCountry'  => 'RU',
        ],
    ],
    'potentialAction' => [
        '@type'  => 'SearchAction',
        'target' => [
            '@type'       => 'EntryPoint',
            'urlTemplate' => url('/posts') . '?search={search_term_string}',
        ],
        'query-input' => 'required name=search_term_string',
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@endsection
