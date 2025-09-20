@extends('layouts.app')

@section('title', 'Категории — Отдых в Карелии')
@section('description', 'Все категории: достопримечательности и места отдыха Карелии с количеством материалов в каждой категории.')

@section('content')
	<div class="container my-4">
		<h1 class="h3 mb-3"><i class="fas fa-folder-open text-primary me-2"></i>Категории</h1>

		@if($categories->count() === 0)
			<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Категории пока не созданы.</div>
		@else
			<div class="row g-3">
				@foreach($categories as $category)
					<div class="col-12 col-md-6 col-lg-4">
						<a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none">
							<div class="card h-100 shadow-sm">
								<div class="card-body">
									<h2 class="h5 mb-2">{{ $category->name }}</h2>
									<p class="text-muted mb-0">
										<i class="far fa-file-alt me-1"></i>{{ $category->posts_count }} {{ \Illuminate\Support\Str::plural('материал', $category->posts_count) }}
									</p>
								</div>
							</div>
						</a>
					</div>
				@endforeach
			</div>
		@endif
	</div>
@endsection


