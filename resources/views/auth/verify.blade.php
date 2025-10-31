@extends('layouts.app')

@section('title', 'Подтверждение email — Отдых в Карелии')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h4 mb-3"><i class="fas fa-envelope me-2"></i>Подтвердите ваш email</h2>
                    <p class="text-muted">Мы отправили письмо с подтверждением на ваш адрес. Перейдите по ссылке в письме, чтобы завершить регистрацию.</p>

                    @if (session('dev_verification_url'))
                        <div class="alert alert-info">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-tools me-2 mt-1"></i>
                                <div>
                                    <strong>DEV-режим:</strong> Вы можете подтвердить email по этой ссылке:
                                    <div class="mt-2">
                                        <a href="{{ session('dev_verification_url') }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">Подтвердить сейчас</a>
                                    </div>
                                    <small class="text-muted d-block mt-1">Ссылка действительна 120 минут. В продакшене блок будет скрыт.</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('status') === 'verification-link-sent' || session('resent'))
                        <div class="alert alert-success" role="alert">
                            Новое письмо с подтверждением отправлено на ваш email.
                        </div>
                    @endif

                    <div class="alert alert-info">
                        Мы отправили письмо на <strong>{{ optional(auth()->user())->email }}</strong>.
                        Проверьте «Входящие». Если письма нет в течение 2–3 минут — загляните в папку «Спам»
                        и отметьте «Не спам». Нажмите ниже, чтобы отправить письмо ещё раз.
                    </div>

                    @php $resendRoute = \Illuminate\Support\Facades\Route::has('verification.send') ? 'verification.send' : 'verification.resend'; @endphp
                    <form class="d-inline" method="POST" action="{{ route($resendRoute) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Отправить письмо ещё раз
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
