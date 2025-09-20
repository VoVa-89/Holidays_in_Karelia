@extends('layouts.app')

@section('title', 'Редактировать категорию — ' . $category->name)
@section('description', 'Админка: редактирование категории ' . $category->name)

@section('content')
	<div class="container my-4">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
				<li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Категории</a></li>
				<li class="breadcrumb-item active" aria-current="page">Редактирование</li>
			</ol>
		</nav>

		<h1 class="h3 mb-3"><i class="fas fa-edit text-primary me-2"></i>Редактировать категорию</h1>

		<div class="card shadow-sm">
			<div class="card-body">
				<form method="POST" action="{{ route('categories.update', $category->slug) }}">
					@csrf
					@method('PUT')

					<div class="mb-3">
						<label for="name" class="form-label">Название<span class="text-danger">*</span></label>
						<input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror" maxlength="255" required>
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Описание</label>
						<textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" maxlength="1000" placeholder="Краткое описание категории">{{ old('description', $category->description) }}</textarea>
						@error('description')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<div class="form-text">Slug обновится автоматически, если вы измените название.</div>
					</div>

					<div class="d-flex flex-wrap gap-2">
						<button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Сохранить</button>
						<a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-secondary">Отмена</a>
						<span class="ms-auto align-self-center text-muted">Материалов в категории: {{ $category->posts()->count() }}</span>
					</div>
				</form>

				@if($category->posts()->count() === 0)
					<hr class="my-4">
					<form method="POST" action="{{ route('categories.destroy', $category->slug) }}" onsubmit="return confirm('Удалить категорию? Отменить будет невозможно.');">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash-alt me-2"></i>Удалить категорию</button>
						<div class="form-text">Удаление невозможно, если в категории есть посты.</div>
					</form>
				@else
					<hr class="my-4">
					<div class="alert alert-warning mb-0">
						<i class="fas fa-exclamation-triangle me-2"></i>Нельзя удалить категорию, в которой есть посты. Переместите или удалите посты.
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection


