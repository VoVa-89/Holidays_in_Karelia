# Middleware для проверки прав администратора

В приложении доступны два middleware для проверки прав администратора:

## 1. AdminMiddleware (алиас: 'admin')

**Файл:** `app/Http/Middleware/AdminMiddleware.php`

**Особенности:**
- Простая проверка прав администратора
- Редирект на главную страницу при отсутствии прав
- Подходит для web-маршрутов

**Использование:**
```php
// В маршрутах
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard']);
});

// Или для отдельного маршрута
Route::get('/admin/settings', [AdminController::class, 'settings'])->middleware('admin');
```

## 2. CheckAdmin (алиас: 'check.admin')

**Файл:** `app/Http/Middleware/CheckAdmin.php`

**Особенности:**
- Поддержка как web, так и API запросов
- JSON ответы для API запросов
- Более гибкая обработка ошибок
- Подходит для REST API

**Использование:**
```php
// Для web-маршрутов
Route::middleware(['auth', 'check.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard']);
});

// Для API маршрутов
Route::middleware(['auth:sanctum', 'check.admin'])->prefix('api/admin')->group(function () {
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::post('/posts/{id}/approve', [AdminController::class, 'approvePost']);
});

// Для отдельного маршрута
Route::get('/admin/statistics', [AdminController::class, 'statistics'])->middleware('check.admin');
```

## Различия между middleware

| Функция | AdminMiddleware | CheckAdmin |
|---------|----------------|------------|
| Web редиректы | ✅ | ✅ |
| API JSON ответы | ❌ | ✅ |
| Гибкая обработка ошибок | ❌ | ✅ |
| Поддержка API маршрутов | ❌ | ✅ |
| Простота использования | ✅ | ✅ |

## Примеры ответов

### AdminMiddleware
- **Неаутентифицированный:** Редирект на `/login`
- **Не администратор:** Редирект на `/` с сообщением об ошибке

### CheckAdmin
- **Web запрос (неаутентифицированный):** Редирект на `/login`
- **Web запрос (не администратор):** Редирект на `/` с сообщением об ошибке
- **API запрос (неаутентифицированный):** JSON `{"error": "Unauthorized", "message": "...", "code": 401}`
- **API запрос (не администратор):** JSON `{"error": "Forbidden", "message": "...", "code": 403}`

## Рекомендации по использованию

1. **Для простых web-маршрутов:** Используйте `AdminMiddleware` (алиас `admin`)
2. **Для API или сложной логики:** Используйте `CheckAdmin` (алиас `check.admin`)
3. **Всегда комбинируйте с `auth`:** `['auth', 'admin']` или `['auth:sanctum', 'check.admin']`

## Проверка в контроллерах

Дополнительно можно использовать метод модели User:

```php
// В контроллере
if (!auth()->user()->isAdmin()) {
    abort(403, 'У вас нет прав для выполнения этого действия');
}

// Или через Gate
if (!Gate::allows('admin')) {
    abort(403);
}
```

