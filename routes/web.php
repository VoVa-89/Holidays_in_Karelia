<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyPostsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Главная страница - лендинг с картой
Route::get('/', [HomeController::class, 'index'])->name('home');

// Аутентификация
Auth::routes();

// Маршруты для постов
Route::prefix('posts')->name('posts.')->group(function () {
    // CRUD операции (только для аутентифицированных)
    Route::middleware('auth')->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::get('/{slug}/edit', [PostController::class, 'edit'])->name('edit');
        Route::put('/{slug}', [PostController::class, 'update'])->name('update');
        Route::delete('/{slug}', [PostController::class, 'destroy'])->name('destroy');
    });

    // Список постов (доступно всем) - должен быть после create
    Route::get('/', [PostController::class, 'index'])->name('index');

    // Просмотр поста (доступно всем) - должен быть последним
    Route::get('/{slug}', [PostController::class, 'show'])->name('show');
});

// Маршруты для управления постами пользователя
Route::middleware('auth')->prefix('my-posts')->name('my-posts.')->group(function () {
    Route::get('/', [MyPostsController::class, 'index'])->name('index');
});

// Маршруты для категорий
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');

    // CRUD операции (только для администраторов) — ДОЛЖНЫ БЫТЬ ДО динамического /{slug}
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{slug}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{slug}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{slug}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Должен быть последним, иначе перехватит /create и /{slug}/edit
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
});

// Маршруты для комментариев (только для аутентифицированных)
Route::middleware('auth')->prefix('comments')->name('comments.')->group(function () {
    Route::post('/posts/{postSlug}', [CommentController::class, 'store'])->name('store');
    Route::delete('/{id}', [CommentController::class, 'destroy'])->name('destroy');
});

// Маршруты для оценок (только для аутентифицированных)
Route::middleware('auth')->prefix('ratings')->name('ratings.')->group(function () {
    Route::post('/posts/{postSlug}', [RatingController::class, 'store'])->name('store');
});

// Админ-панель (только для администраторов)
Route::middleware(['auth', 'check.admin'])->prefix('admin')->name('admin.')->group(function () {
    // Главная страница админ-панели
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Модерация постов
    Route::get('/moderation', [AdminController::class, 'moderation'])->name('moderation');
    Route::post('/posts/{id}/approve', [AdminController::class, 'approvePost'])->name('posts.approve');
    Route::post('/posts/{id}/reject', [AdminController::class, 'rejectPost'])->name('posts.reject');
});

// Дополнительные маршруты для совместимости
Route::get('/home', [HomeController::class, 'index'])->name('home.legacy');
