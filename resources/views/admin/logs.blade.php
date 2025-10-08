@extends('layouts.admin')

@section('title', 'Логи приложения')
@section('description', 'Мониторинг логов Laravel: просмотр, фильтрация и скачивание.')

@section('content')
	<div class="container-fluid my-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h1 class="h4 mb-0"><i class="fas fa-file-alt text-primary me-2"></i>Логи приложения</h1>
			<div class="btn-group">
				@if($selectedFile)
					<a href="{{ route('admin.logs.download', ['file' => $selectedFile]) }}" class="btn btn-outline-secondary btn-sm">
						<i class="fas fa-download me-1"></i>Скачать
					</a>
				@endif
				<button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
					<i class="fas fa-trash me-1"></i>Очистить
				</button>
				<div class="btn-group" role="group">
					<button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
						<i class="fas fa-sync-alt me-1"></i>Автообновление
					</button>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="#" onclick="setAutoRefresh(0)">Выключить</a></li>
						<li><a class="dropdown-item" href="#" onclick="setAutoRefresh(5)">Каждые 5 сек</a></li>
						<li><a class="dropdown-item" href="#" onclick="setAutoRefresh(10)">Каждые 10 сек</a></li>
						<li><a class="dropdown-item" href="#" onclick="setAutoRefresh(30)">Каждые 30 сек</a></li>
						<li><a class="dropdown-item" href="#" onclick="setAutoRefresh(60)">Каждую минуту</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-body">
				<form method="GET" class="row g-3 align-items-end">
					<div class="col-md-4">
						<label class="form-label">Файл</label>
						<select name="file" class="form-select" onchange="this.form.submit()">
							@forelse($files as $f)
								<option value="{{ $f['name'] }}" {{ $selectedFile === $f['name'] ? 'selected' : '' }}>
									{{ $f['name'] }} — {{ number_format($f['size']/1024, 0, ',', ' ') }} KB
								</option>
							@empty
								<option>Логи не найдены</option>
							@endforelse
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">Уровень</label>
						<select name="level" class="form-select" onchange="this.form.submit()">
							<option value="" {{ $level==='' ? 'selected' : '' }}>Любой</option>
							<option value="error" {{ $level==='error' ? 'selected' : '' }}>ERROR</option>
							<option value="warning" {{ $level==='warning' ? 'selected' : '' }}>WARNING</option>
							<option value="notice" {{ $level==='notice' ? 'selected' : '' }}>NOTICE</option>
							<option value="info" {{ $level==='info' ? 'selected' : '' }}>INFO</option>
							<option value="debug" {{ $level==='debug' ? 'selected' : '' }}>DEBUG</option>
							<option value="critical" {{ $level==='critical' ? 'selected' : '' }}>CRITICAL</option>
							<option value="alert" {{ $level==='alert' ? 'selected' : '' }}>ALERT</option>
							<option value="emergency" {{ $level==='emergency' ? 'selected' : '' }}>EMERGENCY</option>
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">Строк (хвост)</label>
						<select name="tail" class="form-select" onchange="this.form.submit()">
							@foreach([200,500,1000,2000] as $t)
								<option value="{{ $t }}" {{ $tail===$t ? 'selected' : '' }}>{{ $t }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Поиск</label>
						<input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="текст...">
					</div>
					<div class="col-12">
						<button class="btn btn-primary"><i class="fas fa-search me-1"></i>Применить</button>
					</div>
				</form>
			</div>
		</div>

		@if(empty($lines))
			<div class="alert alert-secondary">Нет строк для отображения.</div>
		@else
			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<span class="small text-muted">Файл: <strong>{{ $selectedFile }}</strong>, строк: {{ number_format(count($lines), 0, ',', ' ') }}</span>
					<a class="btn btn-sm btn-outline-secondary" href="#" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fas fa-arrow-up"></i></a>
				</div>
				<div class="card-body p-0">
					<pre class="m-0 p-3" style="background:#0f172a;color:#e2e8f0;white-space:pre-wrap;max-height:70vh;overflow:auto;font-size:.875rem;">
@foreach($lines as $l)
{!! highlight($l['text'], $l['level']) !!}
@endforeach
					</pre>
				</div>
			</div>
		@endif
	</div>

	<!-- Модальное окно подтверждения очистки логов -->
	<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title" id="clearLogsModalLabel">
						<i class="fas fa-exclamation-triangle me-2"></i>Очистка логов
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				</div>
				<div class="modal-body">
					<div class="alert alert-warning">
						<i class="fas fa-exclamation-triangle me-2"></i>
						<strong>Внимание!</strong> Это действие необратимо.
					</div>
					<p>Вы действительно хотите очистить все логи приложения?</p>
					<p class="text-muted small">
						Будут удалены все файлы логов из директории <code>storage/logs/</code>
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fas fa-times me-2"></i>Отмена
					</button>
					<form method="POST" action="{{ route('admin.logs.clear') }}" class="d-inline">
						@csrf
						<button type="submit" class="btn btn-danger">
							<i class="fas fa-trash me-2"></i>Очистить логи
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@php
function highlight($text, $level) {
    $text = e($text);
    if (in_array($level, ['error', 'critical', 'alert', 'emergency'], true)) {
        return "<span style=\"color:#fecaca\">{$text}</span>";
    }
    if (in_array($level, ['warning', 'notice'], true)) {
        return "<span style=\"color:#fde68a\">{$text}</span>";
    }
    if ($level === 'info') {
        return "<span style=\"color:#93c5fd\">{$text}</span>";
    }
    if ($level === 'debug') {
        return "<span style=\"color:#a7f3d0\">{$text}</span>";
    }
    return $text;
}
@endphp

@push('scripts')
<script>
let autoRefreshInterval = null;
let autoRefreshSeconds = 0;

function setAutoRefresh(seconds) {
    autoRefreshSeconds = seconds;
    
    // Очищаем предыдущий интервал
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
    
    // Устанавливаем новый интервал
    if (seconds > 0) {
        autoRefreshInterval = setInterval(function() {
            // Обновляем страницу с сохранением параметров
            const url = new URL(window.location);
            window.location.reload();
        }, seconds * 1000);
        
        // Обновляем текст кнопки
        const button = document.querySelector('[data-bs-toggle="dropdown"]');
        button.innerHTML = `<i class="fas fa-sync-alt me-1"></i>Автообновление (${seconds}с)`;
    } else {
        // Сбрасываем текст кнопки
        const button = document.querySelector('[data-bs-toggle="dropdown"]');
        button.innerHTML = `<i class="fas fa-sync-alt me-1"></i>Автообновление`;
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Показываем индикатор автообновления если активно
    if (autoRefreshSeconds > 0) {
        const button = document.querySelector('[data-bs-toggle="dropdown"]');
        button.innerHTML = `<i class="fas fa-sync-alt me-1"></i>Автообновление (${autoRefreshSeconds}с)`;
    }
});
</script>
@endpush


