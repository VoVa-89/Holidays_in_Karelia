@extends('layouts.app')

@section('title', 'Тег «' . $tag->name . '» — Отдых в Карелии')
@section('description', 'Места и достопримечательности Карелии с тегом «' . $tag->name . '».')
@section('og:title', 'Тег «' . $tag->name . '» — Отдых в Карелии')
@section('og:description', 'Места и достопримечательности Карелии с тегом «' . $tag->name . '».')

@section('content')
<div class="container my-4">
	<nav aria-label="breadcrumb" class="mb-3">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
			<li class="breadcrumb-item"><a href="{{ route('posts.index') }}">Посты</a></li>
			<li class="breadcrumb-item active" aria-current="page">{{ $tag->name }}</li>
		</ol>
	</nav>

	<h1 class="h3 mb-4">
		<i class="fas fa-tag text-primary me-2"></i>{{ $tag->name }}
	</h1>

	@if($posts->isEmpty())
		<div class="alert alert-info">Постов с этим тегом пока нет.</div>
	@else
		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
			@foreach($posts as $post)
				<div class="col">
					<x-post-card :post="$post" />
				</div>
			@endforeach
		</div>

		<div class="mt-4">
			{{ $posts->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
		</div>
	@endif
</div>
@endsection
