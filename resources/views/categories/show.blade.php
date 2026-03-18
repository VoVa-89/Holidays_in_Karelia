@extends('layouts.app')

@section('title', $category->name . ' — Отдых в Карелии')
@section('description', ($category->description ?: ('Публикации в категории ' . $category->name)) . ' — Отдых в Карелии')

@section('content')
	<div class="container my-4">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
				<li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Категории</a></li>
				<li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
			</ol>
		</nav>

		<h1 class="h3 mb-3"><i class="fas fa-folder text-primary me-2"></i>{{ $category->name }}</h1>

		@if(!empty($category->description))
			<p class="text-muted">{{ $category->description }}</p>
		@endif

		@if($posts->count() === 0)
			<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>В этой категории пока нет публикаций.</div>
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
@push('schema')
@php
$catSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'CollectionPage',
    'name'        => $category->name . ' — Отдых в Карелии',
    'description' => $category->description
        ?: ('Публикации в категории ' . $category->name . ' — Отдых в Карелии'),
    'url'         => route('categories.show', $category->slug),
    'inLanguage'  => 'ru-RU',
    'isPartOf'    => [
        '@type' => 'WebSite',
        'name'  => 'Отдых в Карелии',
        'url'   => url('/'),
    ],
];

$catBreadcrumb = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => 'Главная',
            'item'     => route('home'),
        ],
        [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => $category->name,
            'item'     => route('categories.show', $category->slug),
        ],
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($catSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($catBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@endsection


