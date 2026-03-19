@extends('layouts.app')

@section('title', ($currentCategory ? $currentCategory->name . ' — ' : '') . 'Посты — Отдых в Карелии')
@section('description', $currentCategory
    ? 'Места и достопримечательности Карелии в категории «' . $currentCategory->name . '» с фото и рейтингами.'
    : 'Список постов: достопримечательности и места отдыха Карелии с фото, описаниями и рейтингами.'
)

@section('content')
	<div class="container my-4">
		<h1 class="h3 mb-3"><i class="fas fa-compass text-primary me-2"></i>Посты</h1>

		<form method="GET" action="{{ route('posts.index') }}" class="card mb-4">
			<div class="card-body">
				<div class="row g-2 align-items-center">
					<div class="col-md-4">
						<label class="form-label">Категория</label>
						<div class="d-flex flex-wrap gap-2">
						@php
							$current = request('category');
						@endphp
						<a href="{{ route('posts.index', array_merge(request()->except(['page', 'search']), ['category' => 'dostoprimechatelnosti'])) }}" class="btn btn-success {{ $current === 'dostoprimechatelnosti' ? 'active' : '' }}"><i class="fas fa-landmark me-2"></i>Достопримечательности</a>
						<a href="{{ route('posts.index', array_merge(request()->except(['page', 'search']), ['category' => 'mesta-otdykha'])) }}" class="btn btn-primary {{ $current === 'mesta-otdykha' ? 'active' : '' }}"><i class="fas fa-campground me-2"></i>Места отдыха</a>
						<a href="{{ route('posts.index', request()->except(['page', 'category', 'search'])) }}" class="btn btn-yellow {{ !$current ? 'active' : '' }}"><i class="fas fa-list me-2"></i>Все посты</a>
						</div>
					</div>
					<div class="col-md-4">
						<label for="search" class="form-label">Поиск по названию</label>
						<input type="text" id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Например: Кижи">
					</div>
					<div class="col-md-3">
						<label for="sort" class="form-label">Сортировка</label>
						<select id="sort" name="sort" class="form-select">
							@php $sort = request('sort', 'created_at'); @endphp
							<option value="created_at" {{ $sort === 'created_at' ? 'selected' : '' }}>По дате (новые)</option>
							<option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>По рейтингу (высокие)</option>
						</select>
						<input type="hidden" name="direction" value="desc">
					</div>
					<div class="col-md-1 text-end">
						<button class="btn btn-primary w-100" type="submit"><i class="fas fa-search"></i></button>
					</div>
				</div>
			</div>
		</form>

		@if(request('tag'))
			<div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
				<div>
					<i class="fas fa-filter me-2"></i>
					Показаны посты с тегом:
					<span class="badge bg-primary">
						{{ request('tag') }}
					</span>
				</div>
				<a href="{{ route('posts.index', request()->except(['tag', 'page'])) }}"
				   class="btn btn-primary">
					<i class="fas fa-times me-1"></i>Сбросить фильтр по тегу
				</a>
			</div>
		@endif

		@if($posts->count() === 0)
			<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>По вашему запросу ничего не найдено.</div>
		@else
			<div class="row g-4">
				@foreach($posts as $post)
					<div class="col-md-6 col-lg-6">
						<x-post-card :post="$post" :showFooter="true" />
					</div>
				@endforeach
			</div>

			<div class="mt-4">
				{{ $posts->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
			</div>
		@endif
	</div>
@endsection
