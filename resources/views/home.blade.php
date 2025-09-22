@extends('layouts.app')

@section('title', 'Отдых в Карелии — Главная')
@section('description', 'Туристический блог и гид по Карелии: статьи, маршруты, достопримечательности, места отдыха и рейтинги.')

@section('content')

	<div class="container my-5">
		<!-- Hero Section -->
		<div class="row align-items-center mb-5">
			<div class="col-lg-7 mb-4 mb-lg-0">
				<h1 class="display-5 fw-bold mb-3" style="white-space: nowrap;">
					<i class="fas fa-mountain text-primary me-2"></i>
					Открой Карелию вместе с&nbsp;нами
				</h1>
				<p class="lead text-muted mb-4">
					Маршруты, достопримечательности и лучшие места отдыха. Реальные отзывы, фото и удобная карта с метками.
				</p>
				<div class="d-flex flex-wrap gap-2 mb-3">
					<a href="{{ route('posts.index', ['category' => 'dostoprimechatelnosti']) }}" class="btn btn-success">
						<i class="fas fa-landmark me-2"></i>Достопримечательности
					</a>
					<a href="{{ route('posts.index', ['category' => 'mesta-otdykha']) }}" class="btn btn-primary">
						<i class="fas fa-campground me-2"></i>Места отдыха
					</a>
					<a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
						<i class="fas fa-list me-2"></i>Все посты
					</a>
				</div>
				
				<!-- Описания категорий -->
				<div class="row g-3">
					<div class="col-md-6">
						<div class="card border-success h-100">
							<div class="card-body">
								<h6 class="card-title text-success mb-2">
									<i class="fas fa-landmark me-2"></i>Достопримечательности
								</h6>
								<p class="card-text small text-muted mb-0">
									Уникальные природные и рукотворные объекты, представляющие культурную, историческую или природную ценность. Места, которые стоит посетить для знакомства с наследием и красотой Карелии.
								</p>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card border-primary h-100">
							<div class="card-body">
								<h6 class="card-title text-primary mb-2">
									<i class="fas fa-campground me-2"></i>Места отдыха
								</h6>
								<p class="card-text small text-muted mb-0">
									Объекты инфраструктуры и локации, созданные для комфортного проведения досуга. Места, где можно остановиться, отдохнуть и получить услуги для полноценного отдыха в Карелии.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="card shadow-sm h-100">
					<div class="card-body">
						<h5 class="card-title mb-3"><i class="fas fa-bullhorn me-2"></i>Добро пожаловать!</h5>
						@guest
							<p class="mb-3">Создайте аккаунт, чтобы добавлять места, оставлять комментарии и ставить оценки.</p>
							<a href="{{ route('register') }}" class="btn btn-success me-2"><i class="fas fa-user-plus me-2"></i>Регистрация</a>
							<a href="{{ route('login') }}" class="btn btn-outline-secondary"><i class="fas fa-sign-in-alt me-2"></i>Войти</a>
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

@endsection
