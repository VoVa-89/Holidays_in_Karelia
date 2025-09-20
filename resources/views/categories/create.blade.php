@extends('layouts.app')

@section('title', 'Создать категорию — Отдых в Карелии')
@section('description', 'Админка: создание новой категории для группировки постов.')

@section('content')
	<div class="container my-4">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
				<li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Категории</a></li>
				<li class="breadcrumb-item active" aria-current="page">Создание</li>
			</ol>
		</nav>

		<h1 class="h3 mb-3"><i class="fas fa-plus-circle text-primary me-2"></i>Создать категорию</h1>

		<div class="card shadow-sm">
			<div class="card-body">
				<form method="POST" action="{{ route('categories.store') }}">
					@csrf

					<div class="mb-3">
						<label for="name" class="form-label">Название<span class="text-danger">*</span></label>
						<input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" maxlength="255" required>
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Описание</label>
						<textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" maxlength="1000" placeholder="Краткое описание категории">{{ old('description') }}</textarea>
						@error('description')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<div class="form-text">Slug сформируется автоматически из названия.</div>
					</div>

					<div class="d-flex gap-2">
						<button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Создать</button>
						<a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Отмена</a>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection


