@props(['post', 'comments' => null])

@php
    $comments = $comments ?? $post->comments()->with('user')->latest()->paginate(10);
@endphp

<div class="comments-section mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-comments text-primary me-2"></i>
            Комментарии 
            <span class="badge bg-secondary">{{ $comments->total() }}</span>
        </h4>
    </div>

    @auth
        <!-- Форма добавления комментария -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('comments.store', $post->slug) }}" id="comment-form">
                    @csrf
                    <div class="mb-3">
                        <label for="content" class="form-label">
                            <i class="fas fa-edit me-1"></i>Добавить комментарий
                        </label>
                        <textarea name="content" id="content" class="form-control" rows="4" 
                                  placeholder="Поделитесь своими впечатлениями..." required></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Панель смайликов -->
                    <div class="mb-3">
                        <div class="emoji-panel">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Добавить смайлик:</small>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="emoji-picker-btn">
                                    <i class="fas fa-smile me-1"></i>Выбрать смайлик
                                </button>
                            </div>
                            <div id="emoji-picker" class="emoji-picker-container mt-2" style="display: none;">
                                <div class="emoji-categories">
                                    <button type="button" class="emoji-category-btn active" data-category="smileys">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                    <button type="button" class="emoji-category-btn" data-category="people">
                                        <i class="fas fa-user"></i>
                                    </button>
                                    <button type="button" class="emoji-category-btn" data-category="nature">
                                        <i class="fas fa-leaf"></i>
                                    </button>
                                    <button type="button" class="emoji-category-btn" data-category="food">
                                        <i class="fas fa-utensils"></i>
                                    </button>
                                    <button type="button" class="emoji-category-btn" data-category="activity">
                                        <i class="fas fa-futbol"></i>
                                    </button>
                                    <button type="button" class="emoji-category-btn" data-category="travel">
                                        <i class="fas fa-plane"></i>
                                    </button>
                                    <button type="button" class="emoji-category-btn" data-category="objects">
                                        <i class="fas fa-gift"></i>
                                    </button>
                                    <button type="button" class="emoji-category-btn" data-category="symbols">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                                <div class="emoji-grid" id="emoji-grid">
                                    <!-- Эмодзи будут загружены через JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Комментарий будет опубликован после модерации
                        </small>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Отправить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- Призыв к регистрации для неавторизованных -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <h5>Войдите, чтобы оставить комментарий</h5>
                <p class="text-muted">Только зарегистрированные пользователи могут комментировать посты.</p>
                <div class="btn-group">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Войти
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Регистрация
                    </a>
                </div>
            </div>
        </div>
    @endauth

    <!-- Список комментариев -->
    @if($comments->count() > 0)
        <div class="comments-list">
            @foreach($comments as $comment)
                <div class="comment-item card mb-3" id="comment-{{ $comment->id }}">
                    <div class="card-body">
                        <div class="d-flex">
                            <!-- Аватар пользователя -->
                            <div class="flex-shrink-0 me-3">
                                <div class="user-avatar bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 48px; height: 48px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            </div>

                            <!-- Содержимое комментария -->
                            <div class="flex-grow-1">
                                <!-- Заголовок комментария -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $comment->user->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $comment->created_at->diffForHumans() }}
                                            <span class="ms-2">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $comment->created_at->format('d.m.Y в H:i') }}
                                            </span>
                                        </small>
                                    </div>

                                    <!-- Действия с комментарием -->
                                    @can('delete', $comment)
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item text-danger delete-comment-btn" 
                                                            data-comment-id="{{ $comment->id }}"
                                                            data-comment-author="{{ $comment->user->name }}">
                                                        <i class="fas fa-trash me-2"></i>Удалить
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    @endcan
                                </div>

                                <!-- Текст комментария -->
                                <div class="comment-content">
                                    <p class="mb-0">{{ $comment->content }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация комментариев -->
        @if($comments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $comments->links() }}
            </div>
        @endif
    @else
        <!-- Пустое состояние -->
        <div class="text-center py-5">
            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Пока нет комментариев</h5>
            <p class="text-muted">Станьте первым, кто поделится своим мнением!</p>
        </div>
    @endif
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Удаление комментария</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить комментарий от <strong id="comment-author-name"></strong>?</p>
                <p class="text-muted small">Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="delete-comment-form" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Удалить
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Стили для компонента комментариев -->
<style>
    .comments-section {
        border-top: 1px solid var(--bs-border-color);
        padding-top: 2rem;
    }

    .emoji-panel {
        background-color: var(--bs-light);
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid var(--bs-border-color);
    }

    .emoji-picker-container {
        background: white;
        border: 1px solid var(--bs-border-color);
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 300px;
        overflow: hidden;
    }

    .emoji-categories {
        display: flex;
        padding: 0.5rem;
        border-bottom: 1px solid var(--bs-border-color);
        background-color: var(--bs-light);
    }

    .emoji-category-btn {
        background: none;
        border: none;
        padding: 0.5rem;
        margin: 0 0.25rem;
        border-radius: 0.375rem;
        color: var(--bs-secondary);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .emoji-category-btn:hover,
    .emoji-category-btn.active {
        background-color: var(--bs-primary);
        color: white;
    }

    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 0.25rem;
        padding: 0.75rem;
        max-height: 200px;
        overflow-y: auto;
    }

    .emoji-item {
        background: none;
        border: none;
        padding: 0.5rem;
        border-radius: 0.375rem;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .emoji-item:hover {
        background-color: var(--bs-primary);
        transform: scale(1.1);
    }

    .emoji-item:active {
        transform: scale(0.95);
    }

    .comment-item {
        transition: all 0.3s ease;
        border: 1px solid var(--bs-border-color);
    }

    .comment-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .user-avatar {
        transition: transform 0.2s ease;
    }

    .comment-item:hover .user-avatar {
        transform: scale(1.05);
    }

    .comment-content {
        line-height: 1.6;
        word-wrap: break-word;
    }

    .comment-content p {
        white-space: pre-wrap;
    }

    /* Анимация появления комментариев */
    .comment-item {
        animation: fadeInUp 0.5s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Стили для пагинации */
    .pagination .page-link {
        border-radius: 0.375rem;
        margin: 0 0.125rem;
        border: 1px solid var(--bs-border-color);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    /* Адаптивность */
    @media (max-width: 576px) {
        .emoji-buttons {
            flex-wrap: wrap;
        }
        
        .comment-item .d-flex {
            flex-direction: column;
        }
        
        .user-avatar {
            align-self: flex-start;
            margin-bottom: 1rem;
        }
    }
</style>

<!-- JavaScript для компонента комментариев -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Обработка удаления комментариев
    document.querySelectorAll('.delete-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const authorName = this.dataset.commentAuthor;
            
            document.getElementById('comment-author-name').textContent = authorName;
            document.getElementById('delete-comment-form').action = `/comments/${commentId}`;
            
            new bootstrap.Modal(document.getElementById('deleteCommentModal')).show();
        });
    });

    // AJAX отправка формы комментария
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Comment form submitted');
            console.log('Form action:', this.action);
            
            const formData = new FormData(this);
            console.log('Form data:', Object.fromEntries(formData));
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Показываем индикатор загрузки
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Отправка...';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    // Для ошибок валидации (422) пытаемся получить JSON
                    if (response.status === 422) {
                        return response.json().then(data => {
                            console.log('Validation error data:', data);
                            throw { type: 'validation', data: data };
                        });
                    }
                    
                    // Для других ошибок получаем текст
                    return response.text().then(text => {
                        console.log('Error response text:', text);
                        throw new Error(`HTTP error! status: ${response.status}, text: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Success data:', data);
                if (data.success) {
                    // Показываем уведомление об успехе
                    showNotification('Комментарий успешно добавлен!', 'success');
                    
                    // Очищаем форму
                    this.reset();
                    
                    // Перезагружаем страницу для обновления списка комментариев
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Показываем ошибки валидации
                    if (data.errors) {
                        showValidationErrors(data.errors);
                    } else {
                        showNotification(data.message || 'Произошла ошибка при добавлении комментария', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Обрабатываем ошибки валидации
                if (error.type === 'validation') {
                    if (error.data.errors) {
                        showValidationErrors(error.data.errors);
                    } else {
                        showNotification(error.data.message || 'Ошибка валидации', 'error');
                    }
                } else {
                    showNotification('Произошла ошибка при добавлении комментария', 'error');
                }
            })
            .finally(() => {
                // Восстанавливаем кнопку
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Функция показа уведомлений
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Автоматически скрываем через 5 секунд
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Функция показа ошибок валидации
    function showValidationErrors(errors) {
        console.log('Showing validation errors:', errors);
        
        // Очищаем предыдущие ошибки
        document.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
        
        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = errors[field][0];
                }
            }
        });
        
        // Показываем общее уведомление с первой ошибкой
        const firstError = Object.values(errors)[0][0];
        showNotification(firstError, 'error');
    }
});
</script>

<!-- Подключение расширенного эмодзи-пикера -->
@include('components.emoji-picker-extended')

