<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
	<div class="container">
		<!-- Логотип -->
		<a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
			<i class="fas fa-mountain text-primary me-2"></i>
			<span class="fw-bold">Отдых в Карелии</span>
		</a>

		<!-- Кнопка мобильного меню -->
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
			<span class="navbar-toggler-icon"></span>
		</button>

		<!-- Основное меню -->
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav me-auto">
				<!-- Главная -->
				<li class="nav-item">
					<a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
						<i class="fas fa-home me-1"></i>Главная
					</a>
				</li>

				<!-- Категории -->
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle {{ request()->routeIs('categories.*') ? 'active' : '' }}" 
					   href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
						<i class="fas fa-list me-1"></i>Категории
					</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="{{ route('categories.index') }}">
							<i class="fas fa-th-large me-2"></i>Все категории
						</a></li>
						<li><hr class="dropdown-divider"></li>
						@foreach(\App\Models\Category::orderBy('name')->get() as $category)
							<li><a class="dropdown-item" href="{{ route('categories.show', $category->slug) }}">
								<i class="fas fa-tag me-2"></i>{{ $category->name }}
							</a></li>
						@endforeach
					</ul>
				</li>

				<!-- Посты -->
				<li class="nav-item">
					<a class="nav-link {{ request()->routeIs('posts.*') && !request()->routeIs('posts.create') && !request()->routeIs('posts.edit') ? 'active' : '' }}" 
					   href="{{ route('posts.index') }}">
						<i class="fas fa-newspaper me-1"></i>Посты
					</a>
				</li>

				<!-- Правила публикации -->
				<li class="nav-item">
					<a class="nav-link {{ request()->routeIs('guidelines') ? 'active' : '' }}" href="{{ route('guidelines') }}">
						<i class="fas fa-book-open me-1"></i>Правила
					</a>
				</li>

				<!-- Админ-панель (только для администраторов) -->
				@auth
					@if(auth()->user()->isAdmin())
						<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" 
					   href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
									<i class="fas fa-cog me-1"></i>Админ
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
									<i class="fas fa-tachometer-alt me-2"></i>Панель управления
								</a></li>
								<li><a class="dropdown-item" href="{{ route('admin.moderation') }}">
									<i class="fas fa-clipboard-check me-2"></i>Модерация
									@if(\App\Models\Post::where('status', 'moderation')->count() > 0)
										<span class="badge bg-warning text-dark ms-2">
											{{ \App\Models\Post::where('status', 'moderation')->count() }}
										</span>
									@endif
								</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="{{ route('categories.index') }}">
									<i class="fas fa-th-large me-2"></i>Категории
								</a></li>
								<li><a class="dropdown-item" href="{{ route('categories.create') }}">
									<i class="fas fa-plus me-2"></i>Создать категорию
								</a></li>
								
								<!-- Управление пользователями (только для супер-администраторов) -->
								@if(auth()->user()->isSuperAdmin())
									<li><hr class="dropdown-divider"></li>
									<li><a class="dropdown-item" href="{{ route('admin.users') }}">
										<i class="fas fa-users me-2"></i>Управление пользователями
									</a></li>
								@endif
								@php
									$currentCategory = null;
									if (request()->routeIs('categories.show') || request()->routeIs('categories.edit')) {
										$slug = request()->route('slug');
										if ($slug) {
											$currentCategory = \App\Models\Category::where('slug', $slug)->first();
										}
									}
								@endphp
								@if($currentCategory)
									<li><hr class="dropdown-divider"></li>
									@if($currentCategory->posts()->count() === 0)
										<li>
											<form method="POST" action="{{ route('categories.destroy', $currentCategory->slug) }}" onsubmit="return confirm('Удалить категорию «{{ $currentCategory->name }}»? Отменить будет невозможно.');">
												@csrf
												@method('DELETE')
												<button type="submit" class="dropdown-item text-danger">
													<i class="fas fa-trash-alt me-2"></i>Удалить категорию
												</button>
											</form>
										</li>
									@else
										<li>
											<span class="dropdown-item text-muted">
												<i class="fas fa-ban me-2"></i>Удалить категорию нельзя (есть посты)
											</span>
										</li>
									@endif
								@endif
							</ul>
						</li>
					@endif
				@endauth
			</ul>

			<!-- Правая часть меню -->
			<ul class="navbar-nav">
				@guest
					<!-- Кнопки для неавторизованных пользователей -->
					<li class="nav-item">
						<a class="nav-link" href="{{ route('login') }}">
							<i class="fas fa-sign-in-alt me-1"></i>Войти
						</a>
					</li>
					<li class="nav-item">
						<a class="btn btn-primary btn-sm ms-2" href="{{ route('register') }}">
							<i class="fas fa-user-plus me-1"></i>Регистрация
						</a>
					</li>
				@else
					<!-- Кнопка создания поста -->
					<li class="nav-item">
						<a class="btn btn-cta {{ request()->routeIs('posts.create') ? 'active' : '' }}" 
						   href="{{ route('posts.create') }}">
							<i class="fas fa-plus me-1"></i>Создать пост
						</a>
					</li>

					<!-- Выпадающее меню пользователя -->
					<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle d-flex align-items-center" 
					   href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
							<!-- Аватар пользователя -->
							<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
								 style="width: 32px; height: 32px;">
								<i class="fas fa-user text-white small"></i>
							</div>
						<span class="d-inline">{{ auth()->user()->name }}</span>
						</a>
						<ul class="dropdown-menu dropdown-menu-end">
							<li class="dropdown-header">
								<div class="fw-bold">{{ auth()->user()->name }}</div>
								<small class="text-muted">{{ auth()->user()->email }}</small>
							</li>
							<li><hr class="dropdown-divider"></li>
							
							<!-- Профиль -->
							<li><a class="dropdown-item" href="{{ route('profile.show') }}">
								<i class="fas fa-user me-2"></i>Мой профиль
							</a></li>
							
							<!-- Мои посты -->
							<li><a class="dropdown-item" href="{{ route('my-posts.index') }}">
								<i class="fas fa-newspaper me-2"></i>Мои посты
							</a></li>
							
							<!-- Настройки -->
							<li><a class="dropdown-item" href="{{ route('profile.settings') }}">
								<i class="fas fa-cog me-2"></i>Настройки
							</a></li>
							
							@if(auth()->user()->isAdmin())
								<li><hr class="dropdown-divider"></li>
								<li class="dropdown-header">
									<small class="text-muted">
										@if(auth()->user()->isSuperAdmin())
											Супер-администратор
										@else
											Администратор
										@endif
									</small>
								</li>
								<li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
									<i class="fas fa-tachometer-alt me-2"></i>Панель управления
								</a></li>
								@if(auth()->user()->isSuperAdmin())
									<li><a class="dropdown-item" href="{{ route('admin.users') }}">
										<i class="fas fa-users me-2"></i>Управление пользователями
									</a></li>
								@endif
							@endif
							
							<li><hr class="dropdown-divider"></li>
							
							<!-- Выход -->
							<li>
								<form method="POST" action="{{ route('logout') }}" class="d-inline">
									@csrf
									<button type="submit" class="dropdown-item text-danger">
										<i class="fas fa-sign-out-alt me-2"></i>Выйти
									</button>
								</form>
							</li>
						</ul>
					</li>
				@endguest
			</ul>
		</div>
	</div>
</nav>

<!-- Дополнительные стили для навигации -->
<style>
	.navbar-brand {
		font-size: 1.5rem;
		text-decoration: none;
	}
	
	.navbar-brand:hover {
		color: var(--bs-primary) !important;
	}
	
	.nav-link.active {
		color: var(--bs-primary) !important;
		font-weight: 500;
	}
	
	.nav-link:hover {
		color: var(--bs-primary) !important;
	}

	/* Не переносить пункты меню и экономить место */
	.navbar .nav-link,
	.navbar .dropdown-toggle {
		white-space: nowrap;
	}

	/* Уменьшаем горизонтальные отступы у ссылок */
	.navbar .nav-link { padding-left: .5rem; padding-right: .5rem; }
	
	.dropdown-menu {
		border: none;
		box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
		border-radius: 0.5rem;
	}
	
	.dropdown-item {
		padding: 0.5rem 1rem;
		transition: all 0.2s ease;
	}
	
	.dropdown-item:hover {
		background-color: var(--bs-primary);
		color: white;
	}
	
	.dropdown-item:hover i {
		color: white !important;
	}
	
	.dropdown-header {
		padding: 0.75rem 1rem 0.5rem;
		background-color: var(--bs-light);
		border-radius: 0.5rem 0.5rem 0 0;
	}
	
	.navbar-toggler {
		border: none;
		padding: 0.25rem 0.5rem;
	}
	
	.navbar-toggler:focus {
		box-shadow: none;
	}
	
	/* Анимация для мобильного меню */
	@media (max-width: 991.98px) {
		.navbar-collapse {
			margin-top: 1rem;
			padding-top: 1rem;
			border-top: 1px solid var(--bs-border-color);
			width: 100%;
		}
		
		.navbar-nav .nav-item {
			margin-bottom: 0.5rem;
		}
		
		/* Дропдауны в коллапсе — статичны, полной ширины и прокручиваемы */
		.navbar-nav .dropdown-menu,
		.navbar-nav .dropdown-menu.dropdown-menu-end {
			position: static !important;
			transform: none !important;
			box-shadow: none;
			border: 1px solid var(--bs-border-color);
			margin-top: 0.5rem;
			max-height: 50vh;
			overflow-y: auto;
			width: 100% !important;
			right: auto !important;
			left: auto !important;
		}

		/* Разрешаем перенос строк */
		.navbar-nav { 
			flex-wrap: wrap; 
		}
		
		/* Правая часть меню (кнопка создания поста + профиль) видна и занимает всю ширину */
		.navbar-nav:not(.me-auto) {
			width: 100%;
			margin-top: 0.5rem;
		}
		
		.navbar-nav:not(.me-auto) .nav-item {
			width: 100%;
			margin-bottom: 0.5rem;
		}
		
		/* Кнопки и ссылки в правой части на всю ширину */
		.navbar-nav:not(.me-auto) .btn,
		.navbar-nav:not(.me-auto) .nav-link {
			width: 100%;
			justify-content: flex-start;
		}
		
		/* Дропдаун профиля должен быть виден и кликабелен */
		.navbar-nav:not(.me-auto) .dropdown-toggle {
			width: 100%;
			display: flex;
			align-items: center;
		}
	}
</style>
