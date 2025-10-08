@extends('layouts.admin')

@section('title', 'Панель управления')
@section('description', 'Административная панель: статистика, модерация постов, управление контентом.')

@section('content')
	<div class="container-fluid my-4">
		<div class="row">
			<div class="col-12">
				<div class="d-flex justify-content-between align-items-center mb-4">
					<h1 class="h3 mb-0"><i class="fas fa-tachometer-alt text-primary me-2"></i>Панель администратора</h1>
					<div class="btn-group">
						<a href="{{ route('admin.moderation') }}" class="btn btn-outline-primary">
							<i class="fas fa-clipboard-check me-2"></i>Модерация
						</a>
						<a href="{{ route('admin.logs') }}" class="btn btn-outline-secondary">
							<i class="fas fa-file-alt me-2"></i>Логи
						</a>
						<a href="{{ route('posts.create') }}" class="btn btn-primary">
							<i class="fas fa-plus me-2"></i>Создать пост
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Статистические карточки -->
		<div class="row g-4 mb-4">
			<div class="col-xl-2 col-md-4 col-sm-6">
				<div class="card bg-primary text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between">
							<div>
								<h4 class="card-title">{{ $stats['total_posts'] }}</h4>
								<p class="card-text small">Всего постов</p>
							</div>
							<div class="align-self-center">
								<i class="fas fa-newspaper fa-2x opacity-75"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-2 col-md-4 col-sm-6">
				<div class="card bg-success text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between">
							<div>
								<h4 class="card-title">{{ $stats['published_posts'] }}</h4>
								<p class="card-text small">Опубликовано</p>
							</div>
							<div class="align-self-center">
								<i class="fas fa-check-circle fa-2x opacity-75"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-2 col-md-4 col-sm-6">
				<div class="card bg-warning text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between">
							<div>
								<h4 class="card-title">{{ $stats['moderation_posts'] }}</h4>
								<p class="card-text small">На модерации</p>
							</div>
							<div class="align-self-center">
								<i class="fas fa-clock fa-2x opacity-75"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-2 col-md-4 col-sm-6">
				<div class="card bg-info text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between">
							<div>
								<h4 class="card-title">{{ $stats['total_users'] }}</h4>
								<p class="card-text small">Пользователей</p>
							</div>
							<div class="align-self-center">
								<i class="fas fa-users fa-2x opacity-75"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-2 col-md-4 col-sm-6">
				<div class="card bg-danger text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between">
							<div>
								<h4 class="card-title">{{ $stats['rejected_posts'] }}</h4>
								<p class="card-text small">Отклонено</p>
							</div>
							<div class="align-self-center">
								<i class="fas fa-times-circle fa-2x opacity-75"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-2 col-md-4 col-sm-6">
				<div class="card bg-secondary text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between">
							<div>
								<h4 class="card-title">{{ $stats['active_users'] }}</h4>
								<p class="card-text small">Активных</p>
							</div>
							<div class="align-self-center">
								<i class="fas fa-user-check fa-2x opacity-75"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<!-- Левая колонка -->
			<div class="col-lg-8">
				<!-- График активности по дням -->
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Активность за последние 7 дней</h5>
					</div>
					<div class="card-body">
						<canvas id="activityChart" height="100"></canvas>
					</div>
				</div>

				<!-- Активные пользователи -->
				<div class="card mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="mb-0"><i class="fas fa-users me-2"></i>Активные пользователи</h5>
						<a href="{{ route('admin.users.list') }}" class="btn btn-sm btn-outline-primary">Все пользователи</a>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead class="table-light">
									<tr>
										<th>Пользователь</th>
										<th>Посты</th>
										<th>Комментарии</th>
										<th>Рейтинги</th>
										<th>Последний пост</th>
										<th>Действия</th>
									</tr>
								</thead>
								<tbody>
									@forelse($activeUsers as $user)
										<tr>
											<td>
												<div class="d-flex align-items-center">
													<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
														<i class="fas fa-user text-white small"></i>
													</div>
													<div>
														<strong>{{ $user->name }}</strong>
														<br>
														<small class="text-muted">{{ $user->email }}</small>
													</div>
												</div>
											</td>
											<td>
												<span class="badge bg-primary">{{ $user->posts_count }}</span>
											</td>
											<td>
												<span class="badge bg-info">{{ $user->comments_count }}</span>
											</td>
											<td>
												<span class="badge bg-warning">{{ $user->ratings_count }}</span>
											</td>
											<td>
												@if($user->posts->count() > 0)
													<small class="text-muted">
														{{ $user->posts->first()->created_at->diffForHumans() }}
													</small>
												@else
													<small class="text-muted">Нет постов</small>
												@endif
											</td>
											<td>
												<div class="btn-group btn-group-sm">
													<a href="{{ route('admin.users.posts', $user->id) }}" class="btn btn-outline-primary btn-sm" title="Посты пользователя">
														<i class="fas fa-eye"></i>
													</a>
													@if(Auth::user()->isSuperAdmin())
														<a href="{{ route('admin.users') }}?user={{ $user->id }}" class="btn btn-outline-warning btn-sm" title="Управление">
															<i class="fas fa-cog"></i>
														</a>
														@if($user->id !== Auth::id() && $user->role !== 'superadmin')
															<button type="button" 
																	class="btn btn-outline-danger btn-sm" 
																	title="Удалить пользователя"
																	data-bs-toggle="modal" 
																	data-bs-target="#deleteUserModal{{ $user->id }}">
																<i class="fas fa-trash"></i>
															</button>
														@endif
													@endif
												</div>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="6" class="text-center text-muted py-4">
												<i class="fas fa-users fa-2x mb-2"></i><br>
												Нет активных пользователей
											</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- Временные аккаунты -->
				<div class="card mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>Временные аккаунты</h5>
						<span class="badge bg-warning">{{ $tempUsers->count() }} неверифицированных</span>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead class="table-light">
									<tr>
										<th>Пользователь</th>
										<th>Дата регистрации</th>
										<th>Дней с регистрации</th>
										<th>Время до удаления</th>
										<th>Статус</th>
										<th>Действия</th>
									</tr>
								</thead>
								<tbody>
									@forelse($tempUsers as $tempUser)
										<tr class="{{ $tempUser->is_expired ? 'table-danger' : '' }}">
											<td>
												<div class="d-flex align-items-center">
													<div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
														<i class="fas fa-user-clock text-white small"></i>
													</div>
													<div>
														<strong>{{ $tempUser->name }}</strong>
														<br>
														<small class="text-muted">{{ $tempUser->email }}</small>
													</div>
												</div>
											</td>
											<td>
												<small class="text-muted">
													{{ $tempUser->created_at->format('d.m.Y H:i') }}
												</small>
											</td>
											<td>
												<span class="badge bg-secondary">{{ $tempUser->days_since_registration }} дн.</span>
											</td>
											<td>
												@if($tempUser->is_expired)
													<span class="badge bg-danger">Просрочен</span>
												@elseif($tempUser->days_until_deletion <= 1)
													<span class="badge bg-warning">{{ $tempUser->days_until_deletion }} дн.</span>
												@else
													<span class="badge bg-info">{{ $tempUser->days_until_deletion }} дн.</span>
												@endif
											</td>
											<td>
												@if($tempUser->is_expired)
													<span class="badge bg-danger">
														<i class="fas fa-exclamation-triangle me-1"></i>К удалению
													</span>
												@elseif($tempUser->days_until_deletion <= 1)
													<span class="badge bg-warning">
														<i class="fas fa-clock me-1"></i>Скоро удаление
													</span>
												@else
													<span class="badge bg-success">
														<i class="fas fa-check me-1"></i>Активен
													</span>
												@endif
											</td>
											<td>
												@if(Auth::user()->isSuperAdmin())
													<div class="btn-group btn-group-sm">
														<form method="POST" action="{{ route('admin.temp-users.verify', $tempUser->id) }}" class="d-inline">
															@csrf
															<button type="submit" 
																	class="btn btn-outline-success btn-sm" 
																	title="Подтвердить email вручную"
																	onclick="return confirm('Вы уверены, что хотите подтвердить email пользователя {{ $tempUser->name }}?')">
																<i class="fas fa-check"></i>
															</button>
														</form>
														<button type="button" 
																class="btn btn-outline-danger btn-sm" 
																title="Удалить временный аккаунт"
																data-bs-toggle="modal" 
																data-bs-target="#deleteTempUserModal{{ $tempUser->id }}">
															<i class="fas fa-trash"></i>
														</button>
													</div>
												@endif
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="6" class="text-center text-muted py-4">
												<i class="fas fa-user-check fa-2x mb-2"></i><br>
												Нет временных аккаунтов
											</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- Последние посты -->
				<div class="card mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="mb-0"><i class="fas fa-list me-2"></i>Последние посты</h5>
						<a href="{{ route('posts.index') }}" class="btn btn-sm btn-outline-primary">Все посты</a>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead class="table-light">
									<tr>
										<th>Название</th>
										<th>Категория</th>
										<th>Автор</th>
										<th>Статус</th>
										<th>Просмотры</th>
										<th>Дата</th>
										<th>Действия</th>
									</tr>
								</thead>
								<tbody>
									@forelse($recentModerationPosts as $post)
										<tr>
											<td>
												<a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">
													{{ Str::limit($post->title, 30) }}
												</a>
											</td>
											<td>
												<span class="badge bg-secondary">{{ $post->category->name }}</span>
											</td>
											<td>{{ $post->user->name }}</td>
											<td>
												@switch($post->status)
													@case('published')
														<span class="badge bg-success">Опубликован</span>
														@break
													@case('moderation')
														<span class="badge bg-warning">На модерации</span>
														@break
													@case('draft')
														<span class="badge bg-secondary">Черновик</span>
														@break
												@endswitch
											</td>
											<td>{{ $post->views }}</td>
											<td>{{ $post->created_at->format('d.m.Y') }}</td>
											<td>
												<div class="btn-group btn-group-sm">
													@if($post->status === 'moderation')
														<form method="POST" action="{{ route('admin.posts.approve', $post->id) }}" class="d-inline">
															@csrf
															<button type="submit" class="btn btn-success btn-sm" title="Одобрить">
																<i class="fas fa-check"></i>
															</button>
														</form>
														<form method="POST" action="{{ route('admin.posts.reject', $post->id) }}" class="d-inline">
															@csrf
															<button type="submit" class="btn btn-danger btn-sm" title="Отклонить">
																<i class="fas fa-times"></i>
															</button>
														</form>
													@endif
													<a href="{{ route('posts.edit', $post->slug) }}" class="btn btn-outline-primary btn-sm" title="Редактировать">
														<i class="fas fa-edit"></i>
													</a>
												</div>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="7" class="text-center text-muted py-4">
												<i class="fas fa-inbox fa-2x mb-2"></i><br>
												Нет постов на модерации
											</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- Правая колонка -->
			<div class="col-lg-4">
				<!-- Топ категории -->
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Топ категории</h5>
					</div>
					<div class="card-body">
						@forelse($topCategories as $category)
							<div class="d-flex justify-content-between align-items-center mb-3">
								<div>
									<h6 class="mb-0">{{ $category->name }}</h6>
									<small class="text-muted">{{ $category->posts_count }} постов</small>
								</div>
								<div class="progress" style="width: 60px; height: 8px;">
									<div class="progress-bar" role="progressbar" 
										 style="width: {{ $topCategories->first()->posts_count > 0 ? ($category->posts_count / $topCategories->first()->posts_count) * 100 : 0 }}%">
									</div>
								</div>
							</div>
						@empty
							<p class="text-muted text-center">Нет данных</p>
						@endforelse
					</div>
				</div>

				<!-- Дополнительная статистика -->
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Дополнительная статистика</h5>
					</div>
					<div class="card-body">
						<div class="row g-3">
							<div class="col-6">
								<div class="text-center">
									<h4 class="text-primary mb-0">{{ $stats['total_categories'] }}</h4>
									<small class="text-muted">Категорий</small>
								</div>
							</div>
							<div class="col-6">
								<div class="text-center">
									<h4 class="text-success mb-0">{{ $stats['total_comments'] }}</h4>
									<small class="text-muted">Комментариев</small>
								</div>
							</div>
							<div class="col-6">
								<div class="text-center">
									<h4 class="text-warning mb-0">{{ $stats['total_ratings'] }}</h4>
									<small class="text-muted">Оценок</small>
								</div>
							</div>
							<div class="col-6">
								<div class="text-center">
									<h4 class="text-info mb-0">{{ $stats['draft_posts'] }}</h4>
									<small class="text-muted">Черновиков</small>
								</div>
							</div>
						</div>
						<hr>
						<div class="text-center">
							<h4 class="text-danger mb-0">{{ number_format($stats['average_rating'], 1) }}</h4>
							<small class="text-muted">Средний рейтинг</small>
						</div>
					</div>
				</div>

				<!-- Топ пользователи по рейтингам -->
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0"><i class="fas fa-star me-2"></i>Топ пользователи</h5>
					</div>
					<div class="card-body">
						@forelse($topRatedUsers as $user)
							<div class="d-flex justify-content-between align-items-center mb-3">
								<div>
									<h6 class="mb-0">{{ $user->name }}</h6>
									<small class="text-muted">{{ $user->ratings_count }} оценок</small>
								</div>
								<div class="text-end">
									<div class="d-flex align-items-center">
										@for($i = 1; $i <= 5; $i++)
											@if($i <= $user->ratings_avg_value)
												<i class="fas fa-star text-warning"></i>
											@else
												<i class="far fa-star text-muted"></i>
											@endif
										@endfor
									</div>
									<small class="text-muted">{{ number_format($user->ratings_avg_value, 1) }}</small>
								</div>
							</div>
						@empty
							<p class="text-muted text-center">Нет данных</p>
						@endforelse
					</div>
				</div>

				<!-- Последние рейтинги и отзывы -->
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0"><i class="fas fa-star me-2"></i>Последние рейтинги</h5>
					</div>
					<div class="card-body">
						@forelse($recentRatings as $rating)
							<div class="d-flex mb-3">
								<div class="flex-shrink-0">
									<div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
										<i class="fas fa-star text-white small"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-2">
									<div class="d-flex justify-content-between">
										<strong class="small">{{ $rating->user->name }}</strong>
									<div class="d-flex align-items-center">
										@for($i = 1; $i <= 5; $i++)
											@if($i <= $rating->value)
												<i class="fas fa-star text-warning small"></i>
											@else
												<i class="far fa-star text-muted small"></i>
											@endif
										@endfor
									</div>
									</div>
									<p class="small mb-1 text-muted">Оценка: {{ $rating->value }}/5</p>
									<small class="text-muted">
										К посту: <a href="{{ route('posts.show', $rating->post->slug) }}" class="text-decoration-none">
											{{ Str::limit($rating->post->title, 20) }}
										</a>
									</small>
								</div>
							</div>
						@empty
							<p class="text-muted text-center">Нет рейтингов</p>
						@endforelse
					</div>
				</div>

				<!-- Последние комментарии -->
				<div class="card">
					<div class="card-header">
						<h5 class="mb-0"><i class="fas fa-comments me-2"></i>Последние комментарии</h5>
					</div>
					<div class="card-body">
						@forelse($recentComments as $comment)
							<div class="d-flex mb-3">
								<div class="flex-shrink-0">
									<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
										<i class="fas fa-user text-white small"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-2">
									<div class="d-flex justify-content-between">
										<strong class="small">{{ $comment->user->name }}</strong>
										<small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
									</div>
									<p class="small mb-1">{{ Str::limit($comment->content, 50) }}</p>
									<small class="text-muted">
										К посту: <a href="{{ route('posts.show', $comment->post->slug) }}" class="text-decoration-none">
											{{ Str::limit($comment->post->title, 20) }}
										</a>
									</small>
								</div>
							</div>
						@empty
							<p class="text-muted text-center">Нет комментариев</p>
						@endforelse
					</div>
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<script>
			// График активности по дням
			const ctx = document.getElementById('activityChart').getContext('2d');
			const activityData = @json($postsByDay);
			
			// Подготавливаем данные для графика
			const labels = [];
			const data = [];
			const last7Days = [];
			
			// Создаем массив последних 7 дней
			for (let i = 6; i >= 0; i--) {
				const date = new Date();
				date.setDate(date.getDate() - i);
				const dateStr = date.toISOString().split('T')[0];
				last7Days.push(dateStr);
			}
			
			// Заполняем данные (включая дни с 0 постов)
			last7Days.forEach(date => {
				const dayData = activityData.find(item => item.date === date);
				labels.push(new Date(date).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' }));
				data.push(dayData ? dayData.count : 0);
			});
			
			new Chart(ctx, {
				type: 'line',
				data: {
					labels: labels,
					datasets: [{
						label: 'Постов создано',
						data: data,
						borderColor: 'rgb(13, 110, 253)',
						backgroundColor: 'rgba(13, 110, 253, 0.1)',
						tension: 0.4,
						fill: true
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							display: false
						}
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: {
								stepSize: 1
							}
						}
					}
				}
			});
		</script>
	@endpush

	<!-- Модальные окна подтверждения удаления -->
	@foreach($activeUsers as $user)
		@if(Auth::user()->isSuperAdmin() && $user->id !== Auth::id() && $user->role !== 'superadmin')
			<div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-danger text-white">
							<h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">
								<i class="fas fa-exclamation-triangle me-2"></i>Удаление пользователя
							</h5>
							<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
						</div>
						<div class="modal-body">
							<div class="alert alert-warning">
								<i class="fas fa-exclamation-triangle me-2"></i>
								<strong>Внимание!</strong> Это действие необратимо.
							</div>
							<p>Вы действительно хотите удалить пользователя <strong>{{ $user->name }}</strong>?</p>
							<p class="text-muted small">
								При удалении пользователя будут также удалены:
							</p>
							<ul class="text-muted small">
								<li>Все посты пользователя ({{ $user->posts->count() }})</li>
								<li>Все комментарии пользователя</li>
								<li>Все рейтинги пользователя</li>
								<li>Все фотографии пользователя</li>
							</ul>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
								<i class="fas fa-times me-2"></i>Отмена
							</button>
							<form method="POST" action="{{ route('admin.users.delete', $user->id) }}" class="d-inline">
								@csrf
								@method('DELETE')
								<button type="submit" class="btn btn-danger">
									<i class="fas fa-trash me-2"></i>Удалить пользователя
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		@endif
	@endforeach

	<!-- Модальные окна подтверждения удаления временных аккаунтов -->
	@foreach($tempUsers as $tempUser)
		@if(Auth::user()->isSuperAdmin())
			<div class="modal fade" id="deleteTempUserModal{{ $tempUser->id }}" tabindex="-1" aria-labelledby="deleteTempUserModalLabel{{ $tempUser->id }}" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-danger text-white">
							<h5 class="modal-title" id="deleteTempUserModalLabel{{ $tempUser->id }}">
								<i class="fas fa-exclamation-triangle me-2"></i>Удаление временного аккаунта
							</h5>
							<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
						</div>
						<div class="modal-body">
							<div class="alert alert-warning">
								<i class="fas fa-exclamation-triangle me-2"></i>
								<strong>Внимание!</strong> Это действие необратимо.
							</div>
							<p>Вы действительно хотите удалить временный аккаунт <strong>{{ $tempUser->name }}</strong>?</p>
							<p class="text-muted small">
								При удалении временного аккаунта будут также удалены:
							</p>
							<ul class="text-muted small">
								<li>Все посты пользователя</li>
								<li>Все комментарии пользователя</li>
								<li>Все рейтинги пользователя</li>
								<li>Все фотографии пользователя</li>
							</ul>
							<div class="alert alert-info">
								<i class="fas fa-info-circle me-2"></i>
								<strong>Информация:</strong> Пользователь зарегистрирован {{ $tempUser->days_since_registration }} дней назад.
								@if($tempUser->is_expired)
									<span class="text-danger">Аккаунт просрочен и подлежит удалению.</span>
								@else
									<span class="text-warning">До автоматического удаления: {{ $tempUser->days_until_deletion }} дней.</span>
								@endif
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
								<i class="fas fa-times me-2"></i>Отмена
							</button>
							<form method="POST" action="{{ route('admin.temp-users.delete', $tempUser->id) }}" class="d-inline">
								@csrf
								@method('DELETE')
								<button type="submit" class="btn btn-danger">
									<i class="fas fa-trash me-2"></i>Удалить временный аккаунт
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		@endif
	@endforeach
@endsection
