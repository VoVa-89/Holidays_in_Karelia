@extends('layouts.app')

@section('title', 'Посты — Отдых в Карелии')
@section('description', 'Список постов: достопримечательности и места отдыха Карелии с фото, описаниями и рейтингами.')

@section('content')
	<div class="container my-4">
		<h1 class="h3 mb-3"><i class="fas fa-compass text-primary me-2"></i>Посты</h1>

		<form method="GET" action="{{ route('home') }}" class="card mb-4">
			<div class="card-body">
				<div class="row g-2 align-items-end">
					<div class="col-md-4">
						<label class="form-label">Категория</label>
						<div class="btn-group w-100" role="group">
							@php
								$current = request('category');
							@endphp
							<a href="?{{ http_build_query(array_merge(request()->except('page'), ['category' => 'dostoprimechatelnosti'])) }}" class="btn btn-outline-primary {{ $current === 'dostoprimechatelnosti' ? 'active' : '' }}">Достопримечательности</a>
							<a href="?{{ http_build_query(array_merge(request()->except('page'), ['category' => 'mesta-otdykha'])) }}" class="btn btn-outline-primary {{ $current === 'mesta-otdykha' ? 'active' : '' }}">Места отдыха</a>
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
							<option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>По рейтингу</option>
						</select>
					</div>
					<div class="col-md-1 text-end">
						<button class="btn btn-primary w-100" type="submit"><i class="fas fa-search"></i></button>
					</div>
				</div>
			</div>
		</form>

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
				{{ $posts->onEachSide(1)->links() }}
			</div>
		@endif
	</div>
@endsection
