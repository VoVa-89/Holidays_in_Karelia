@props(['post', 'userRating' => null, 'canVote' => true])

@php
    $userRating = $userRating ?? (auth()->check() ? $post->ratings()->where('user_id', auth()->id())->first()?->value : null);
    $averageRating = $post->rating ?? 0;
    $totalVotes = $post->ratings()->count();
@endphp

<div class="rating-component" data-post-id="{{ $post->id }}">
    <div class="rating-header mb-3">
        <h6 class="mb-1">
            <i class="fas fa-star text-warning me-2"></i>
            Рейтинг места
        </h6>
        <div class="rating-summary">
            <div class="d-flex align-items-center">
                <div class="rating-display me-3">
                    <span class="rating-value">{{ number_format($averageRating, 1) }}</span>
                    <span class="rating-max">/ 5.0</span>
                </div>
                <div class="rating-stars-display">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))
                            <i class="fas fa-star text-warning"></i>
                        @elseif($i - 0.5 <= $averageRating)
                            <i class="fas fa-star-half-alt text-warning"></i>
                        @else
                            <i class="far fa-star text-muted"></i>
                        @endif
                    @endfor
                </div>
                <div class="rating-count ms-3">
                    <small class="text-muted">
                        <i class="fas fa-users me-1"></i>
                        {{ $totalVotes }} {{ Str::plural('оценка', $totalVotes) }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    @auth
        @if($canVote)
            <div class="rating-vote-section">
                <div class="mb-2">
                    <small class="text-muted">
                        @if($userRating)
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Ваша оценка: {{ $userRating }}/5
                        @else
                            <i class="fas fa-vote-yea me-1"></i>
                            Поставьте оценку:
                        @endif
                    </small>
                </div>

                <form method="POST" action="{{ route('ratings.store', $post->slug) }}" id="rating-form" class="rating-form">
                    @csrf
                    <input type="hidden" name="value" id="rating-value" value="{{ $userRating ?? 0 }}">
                    
                    <div class="rating-stars-interactive">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    class="rating-star {{ $userRating && $i <= $userRating ? 'active' : '' }}" 
                                    data-rating="{{ $i }}"
                                    title="Оценить на {{ $i }} {{ Str::plural('звезда', $i) }}">
                                <i class="fas fa-star"></i>
                            </button>
                        @endfor
                    </div>

                    <div class="rating-actions mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary {{ $userRating ? '' : 'd-none' }}" id="change-rating-btn">
                            <i class="fas fa-edit me-1"></i>Изменить оценку
                        </button>

                        <button type="submit"
                                class="btn btn-sm btn-primary ms-0 {{ $userRating ? 'd-none' : '' }}"
                                id="submit-rating-btn"
                                {{ $userRating ? '' : 'disabled' }}>
                            <i class="fas fa-star me-1"></i>
                            <span class="submit-label">{{ $userRating ? 'Сохранить' : 'Оценить' }}</span>
                        </button>

                        @if(!$userRating)
                            <button type="button" class="btn btn-sm btn-outline-warning ms-2" id="quick-rate-5-btn">
                                <i class="fas fa-star me-1"></i>Оценить на 5 звезд
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-info py-2">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Вы не можете оценить собственный пост
                </small>
            </div>
        @endif
    @else
        <div class="rating-guest">
            <div class="alert alert-light py-2">
                <small class="text-muted">
                    <i class="fas fa-sign-in-alt me-1"></i>
                    <a href="{{ route('login') }}" class="text-decoration-none">Войдите</a> 
                    или 
                    <a href="{{ route('register') }}" class="text-decoration-none">зарегистрируйтесь</a> 
                    для оценки
                </small>
            </div>
        </div>
    @endauth

    <!-- Детальная статистика рейтинга -->
    @if($totalVotes > 0)
        <div class="rating-breakdown mt-3">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#ratingBreakdown">
                <i class="fas fa-chart-bar me-1"></i>Детальная статистика
            </button>
            
            <div class="collapse mt-2" id="ratingBreakdown">
                <div class="rating-stats">
                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = $post->ratings()->where('value', $star)->count();
                            $percentage = $totalVotes > 0 ? ($count / $totalVotes) * 100 : 0;
                        @endphp
                        <div class="rating-stat-row">
                            <div class="d-flex align-items-center">
                                <span class="rating-stat-label">{{ $star }}</span>
                                <i class="fas fa-star text-warning ms-1"></i>
                                <div class="rating-stat-bar ms-2 flex-grow-1">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" 
                                             style="width: {{ $percentage }}%"
                                             role="progressbar">
                                        </div>
                                    </div>
                                </div>
                                <span class="rating-stat-count ms-2">{{ $count }}</span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Стили для компонента рейтинга -->
<style>
    .rating-component {
        background: var(--bs-light);
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid var(--bs-border-color);
    }

    .rating-header h6 {
        color: var(--bs-dark);
        font-weight: 600;
    }

    .rating-display {
        display: flex;
        align-items: baseline;
    }

    .rating-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--bs-warning);
        line-height: 1;
    }

    .rating-max {
        font-size: 1rem;
        color: var(--bs-muted);
        margin-left: 0.25rem;
    }

    .rating-stars-display {
        font-size: 1.2rem;
    }

    .rating-stars-display i {
        margin-right: 0.125rem;
    }

    .rating-stars-interactive {
        display: flex;
        gap: 0.25rem;
        margin-bottom: 0.5rem;
    }

    .rating-star {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--bs-muted);
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0.25rem;
        border-radius: 0.375rem;
        position: relative;
    }

    .rating-star:hover {
        color: var(--bs-warning);
        transform: scale(1.1);
        background-color: rgba(255, 193, 7, 0.1);
    }

    .rating-star.active {
        color: var(--bs-warning);
        animation: starGlow 0.3s ease;
    }

    .rating-star:hover ~ .rating-star {
        color: var(--bs-muted);
    }

    .rating-star:hover,
    .rating-star:hover ~ .rating-star {
        color: var(--bs-warning);
    }

    @keyframes starGlow {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .rating-actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .rating-actions.show {
        opacity: 1;
    }

    .rating-stat-row {
        margin-bottom: 0.5rem;
    }

    .rating-stat-label {
        font-weight: 600;
        min-width: 1rem;
    }

    .rating-stat-bar .progress {
        background-color: var(--bs-light);
    }

    .rating-stat-count {
        font-size: 0.875rem;
        color: var(--bs-muted);
        min-width: 2rem;
        text-align: right;
    }

    .rating-form {
        transition: all 0.3s ease;
    }

    .rating-form.submitting {
        opacity: 0.7;
        pointer-events: none;
    }

    /* Анимация успешного голосования */
    .rating-success {
        animation: ratingSuccess 0.6s ease;
    }

    @keyframes ratingSuccess {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); background-color: rgba(40, 167, 69, 0.1); }
        100% { transform: scale(1); }
    }

    /* Адаптивность */
    @media (max-width: 576px) {
        .rating-component {
            padding: 1rem;
        }
        
        .rating-value {
            font-size: 1.5rem;
        }
        
        .rating-stars-interactive {
            gap: 0.25rem;
        }
        
        .rating-star {
            font-size: 1.75rem;    /* крупнее для тача */
            padding: 0.25rem;      /* больше тач‑таргет */
        }

        .rating-actions .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>

<!-- JavaScript для компонента рейтинга -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingComponent = document.querySelector('.rating-component');
    if (!ratingComponent) return;

    const ratingForm = ratingComponent.querySelector('#rating-form');
    const ratingStars = ratingComponent.querySelectorAll('.rating-star');
    const ratingValueInput = ratingComponent.querySelector('#rating-value');
    const submitBtn = ratingComponent.querySelector('#submit-rating-btn');
    const changeBtn = ratingComponent.querySelector('#change-rating-btn');
    const quickRate5Btn = ratingComponent.querySelector('#quick-rate-5-btn');
    const ratingActions = ratingComponent.querySelector('.rating-actions');
    
    let currentRating = {{ $userRating ?? 0 }};
    let isVoting = false;

    // Инициализация - если у пользователя уже есть оценка, активируем кнопку
    if (currentRating > 0) {
        if (ratingActions) {
            ratingActions.classList.add('show');
        }
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    }

    // Обработка наведения/тача и клика по звездам
    ratingStars.forEach((star, index) => {
        star.addEventListener('mouseenter', function() {
            if (isVoting) return;
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });

        // touchstart для мобильных
        star.addEventListener('touchstart', function(e) {
            if (isVoting) return;
            e.preventDefault();
            const rating = parseInt(this.dataset.rating);
            selectRating(rating);
        }, { passive: false });

        star.addEventListener('click', function() {
            if (isVoting) return;
            const rating = parseInt(this.dataset.rating);
            selectRating(rating);
        });
    });

    // Сброс подсветки при уходе мыши
    ratingComponent.addEventListener('mouseleave', function() {
        if (isVoting) return;
        highlightStars(currentRating);
    });

    // Подсветка звезд
    function highlightStars(rating) {
        ratingStars.forEach((star, index) => {
            const starRating = index + 1;
            if (starRating <= rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    // Выбор рейтинга
    function selectRating(rating) {
        console.log('Selecting rating:', rating);
        currentRating = rating;
        ratingValueInput.value = rating;
        
        console.log('Current rating set to:', currentRating);
        console.log('Input value set to:', ratingValueInput.value);
        
        // Показать кнопку отправки
        if (ratingActions) {
            ratingActions.classList.add('show');
        }
        
        if (submitBtn) {
            submitBtn.classList.remove('d-none');
            submitBtn.disabled = false;
        }
        
        // Анимация выбора
        ratingComponent.classList.add('rating-success');
        setTimeout(() => {
            ratingComponent.classList.remove('rating-success');
        }, 600);
    }

    // Обработка отправки формы
    if (ratingForm) {
        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Rating form submitted, currentRating:', currentRating);
            console.log('Form action:', this.action);
            
            if (isVoting || currentRating === 0) {
                console.log('Form submission blocked - isVoting:', isVoting, 'currentRating:', currentRating);
                showRatingNotification('Пожалуйста, выберите оценку перед отправкой', 'error');
                return;
            }
            
            isVoting = true;
            ratingForm.classList.add('submitting');
            
            const formData = new FormData(this);
            console.log('Form data:', Object.fromEntries(formData));
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('CSRF token:', csrfToken);
            
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Отправка...';
                submitBtn.disabled = true;
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    if (response.status === 403) {
                        return response.json().then(data => {
                            const msg = data && data.message ? data.message : 'Для оценки постов необходимо подтвердить email.';
                            throw { type: 'forbidden', message: msg };
                        }).catch(() => {
                            throw { type: 'forbidden', message: 'Для оценки постов необходимо подтвердить email. Проверьте почту или отправьте письмо повторно в профиле.' };
                        });
                    }
                    // Попробуем получить текст ошибки
                    return response.text().then(text => {
                        console.log('Error response text:', text);
                        throw new Error(`HTTP error! status: ${response.status}, text: ${text}`);
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Показать уведомление об успехе
                    showRatingNotification('Оценка успешно сохранена!', 'success');
                    
                    // Обновить отображение рейтинга
                    updateRatingDisplay(data.rating, data.totalVotes);
                    
                    // Скрыть форму голосования
                    hideVotingForm();
                } else {
                    showRatingNotification(data.message || 'Ошибка при сохранении оценки', 'error');
                }
            })
            .catch(error => {
                console.error('Rating submission error:', error);
                if (error.type === 'forbidden') {
                    showRatingNotification(error.message, 'error');
                    return;
                }
                showRatingNotification('Произошла ошибка при сохранении оценки', 'error');
            })
            .finally(() => {
                isVoting = false;
                ratingForm.classList.remove('submitting');
                
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        });
    }

    // Обновление отображения рейтинга
    function updateRatingDisplay(averageRating, totalVotes) {
        const ratingValue = ratingComponent.querySelector('.rating-value');
        const ratingCount = ratingComponent.querySelector('.rating-count small');
        const starsDisplay = ratingComponent.querySelector('.rating-stars-display');
        
        if (ratingValue) {
            ratingValue.textContent = parseFloat(averageRating).toFixed(1);
        }
        
        if (ratingCount) {
            ratingCount.innerHTML = `<i class="fas fa-users me-1"></i>${totalVotes} ${totalVotes === 1 ? 'оценка' : 'оценки'}`;
        }
        
        if (starsDisplay) {
            starsDisplay.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('i');
                if (i <= Math.floor(averageRating)) {
                    star.className = 'fas fa-star text-warning';
                } else if (i - 0.5 <= averageRating) {
                    star.className = 'fas fa-star-half-alt text-warning';
                } else {
                    star.className = 'far fa-star text-muted';
                }
                star.style.marginRight = '0.125rem';
                starsDisplay.appendChild(star);
            }
        }
    }

    // Скрытие формы голосования
    function hideVotingForm() {
        const voteSection = ratingComponent.querySelector('.rating-vote-section');
        if (voteSection) {
            voteSection.style.display = 'none';
        }
        
        // Показать сообщение об успешном голосовании
        const successMessage = document.createElement('div');
        successMessage.className = 'alert alert-success py-2';
        successMessage.innerHTML = '<small><i class="fas fa-check-circle me-1"></i>Спасибо за вашу оценку!</small>';
        
        const voteSectionParent = voteSection ? voteSection.parentNode : ratingComponent;
        voteSectionParent.insertBefore(successMessage, voteSection);
    }

    // Показать уведомление
    function showRatingNotification(message, type) {
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
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Кнопка изменения оценки
    if (changeBtn) {
        changeBtn.addEventListener('click', function() {
            const voteSection = ratingComponent.querySelector('.rating-vote-section');
            if (voteSection) {
                voteSection.style.display = 'block';
            }
            if (submitBtn) {
                submitBtn.classList.remove('d-none');
                const label = submitBtn.querySelector('.submit-label');
                if (label) label.textContent = 'Сохранить';
                submitBtn.disabled = (currentRating === 0);
            }
            if (ratingActions) ratingActions.classList.add('show');
            this.style.display = 'none';
        });
    }

    // Кнопка быстрой оценки на 5 звезд
    if (quickRate5Btn) {
        quickRate5Btn.addEventListener('click', function() {
            selectRating(5);
            // Автоматически отправить форму
            setTimeout(() => {
                if (ratingForm) {
                    ratingForm.dispatchEvent(new Event('submit'));
                }
            }, 100);
        });
    }
});
</script>
