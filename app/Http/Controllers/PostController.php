<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Контроллер для управления постами
 * 
 * Обрабатывает CRUD операции для постов: создание, просмотр,
 * редактирование, удаление с применением политик доступа.
 */
final class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Отображение списка постов с пагинацией и фильтрацией
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Post::with(['user', 'category', 'photos'])
            ->where('status', 'published');

        // Фильтрация по категории (поддержка id или slug)
        if ($request->filled('category')) {
            $categoryParam = (string) $request->get('category');
            $category = Category::query()
                ->when(is_numeric($categoryParam), fn($q) => $q->where('id', (int) $categoryParam))
                ->when(!is_numeric($categoryParam), fn($q) => $q->orWhere('slug', $categoryParam))
                ->first();

            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Поиск по заголовку
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Сортировка
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortBy, ['title', 'rating', 'views', 'created_at'])) {
            if ($sortBy === 'rating') {
                // Для рейтинга заменяем NULL на 0 и сортируем по убыванию
                $query->orderByRaw('COALESCE(rating, 0) DESC')
                      ->orderBy('created_at', 'desc');
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        } else {
            // Сортировка по умолчанию
            $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(4)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('posts.index', compact('posts', 'categories'));
    }

    /**
     * Отображение формы создания поста
     * 
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', Post::class);

        $categories = Category::orderBy('name')->get();
        return view('posts.create', compact('categories'));
    }

    /**
     * Сохранение нового поста
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Post::class);
        
        Log::info('Начало создания поста', [
            'user_id' => Auth::id(),
            'request_data' => $request->except(['_token']),
            'files_count' => $request->hasFile('photos') ? count($request->file('photos')) : 0
        ]);

        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255|unique:posts,title',
            'description' => 'required|string|min:5',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'website_url' => 'nullable|url|max:255',
            'status' => 'in:' . implode(',', Post::STATUSES),
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'main_index' => 'nullable|integer|min:0',
        ], [
            // Кастомные сообщения об ошибках
            'title.required' => 'Название поста обязательно для заполнения.',
            'title.min' => 'Название должно содержать минимум 3 символа.',
            'title.max' => 'Название не может быть длиннее 255 символов.',
            'title.unique' => 'Такое название поста уже существует.',

            'description.required' => 'Описание поста обязательно для заполнения.',
            'description.min' => 'Описание должно содержать минимум 5 символов.',

            'category_id.required' => 'Выберите категорию поста.',
            'category_id.exists' => 'Выбранная категория не существует.',

            'address.required' => 'Адрес места обязателен для заполнения.',
            'address.max' => 'Адрес не может быть длиннее 255 символов.',

            'latitude.required' => 'Укажите координаты места на карте.',
            'latitude.numeric' => 'Широта должна быть числом.',
            'latitude.between' => 'Широта должна быть между -90 и 90 градусами.',

            'longitude.required' => 'Укажите координаты места на карте.',
            'longitude.numeric' => 'Долгота должна быть числом.',
            'longitude.between' => 'Долгота должна быть между -180 и 180 градусами.',

            'status.in' => 'Недопустимый статус поста.',

            'photos.array' => 'Ошибка загрузки фотографий.',
            'photos.*.image' => 'Файл должен быть изображением.',
            'photos.*.mimes' => 'Допустимые форматы: JPEG, PNG, JPG, GIF.',
            'photos.*.max' => 'Размер изображения не должен превышать 5 МБ.',

            'main_index.integer' => 'Некорректный индекс главной фотографии.',
            'main_index.min' => 'Индекс главной фотографии не может быть отрицательным.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);
        $validated['status'] = $validated['status'] ?? Post::STATUS_MODERATION;
        $validated['rejection_reason'] = null;
        $validated['rejected_at'] = null;

        try {
            $post = DB::transaction(function () use ($validated, $request) {
                $post = Post::create($validated);

                // Обработка загруженных фотографий
                if ($request->hasFile('photos')) {
                    $this->handlePhotoUploads($post, $request->file('photos'), $request->get('main_index', 0));
                }

                return $post;
            });
            
            Log::info('Пост успешно создан', [
                'post_id' => $post->id,
                'title' => $post->title,
                'status' => $post->status,
                'user_id' => $post->user_id
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка создания поста: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'validated' => $validated,
                'files_count' => $request->hasFile('photos') ? count($request->file('photos')) : 0,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Произошла ошибка при создании поста. Попробуйте еще раз.');
        }

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Пост успешно создан!');
    }

    /**
     * Отображение поста с увеличением счетчика просмотров
     * 
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $post = Post::with(['user', 'category', 'photos', 'comments.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->authorize('view', $post);

        // Увеличиваем счетчик просмотров
        $post->incrementViews();

        return view('posts.show', compact('post'));
    }

    /**
     * Отображение формы редактирования поста
     * 
     * @param string $slug
     * @return View
     */
    public function edit(string $slug): View
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $this->authorize('update', $post);

        $categories = Category::orderBy('name')->get();
        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Обновление поста
     * 
     * @param Request $request
     * @param string $slug
     * @return RedirectResponse
     */
    public function update(Request $request, string $slug): RedirectResponse
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255|unique:posts,title,' . $post->id,
            'description' => 'required|string|min:5',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'website_url' => 'nullable|url|max:255',
            'status' => 'in:' . implode(',', Post::STATUSES),
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'main_index' => 'nullable|string',
            'main_photo_id' => 'nullable|string',
            'deleted_photos' => 'nullable|string',
        ], [
            // Кастомные сообщения об ошибках
            'title.required' => 'Название поста обязательно для заполнения.',
            'title.min' => 'Название должно содержать минимум 3 символа.',
            'title.max' => 'Название не может быть длиннее 255 символов.',
            'title.unique' => 'Такое название поста уже существует.',

            'description.required' => 'Описание поста обязательно для заполнения.',
            'description.min' => 'Описание должно содержать минимум 5 символов.',

            'category_id.required' => 'Выберите категорию поста.',
            'category_id.exists' => 'Выбранная категория не существует.',

            'address.required' => 'Адрес места обязателен для заполнения.',
            'address.max' => 'Адрес не может быть длиннее 255 символов.',

            'latitude.required' => 'Укажите координаты места на карте.',
            'latitude.numeric' => 'Широта должна быть числом.',
            'latitude.between' => 'Широта должна быть между -90 и 90 градусами.',

            'longitude.required' => 'Укажите координаты места на карте.',
            'longitude.numeric' => 'Долгота должна быть числом.',
            'longitude.between' => 'Долгота должна быть между -180 и 180 градусами.',

            'status.in' => 'Недопустимый статус поста.',

            'photos.array' => 'Ошибка загрузки фотографий.',
            'photos.*.image' => 'Файл должен быть изображением.',
            'photos.*.mimes' => 'Допустимые форматы: JPEG, PNG, JPG, GIF.',
            'photos.*.max' => 'Размер изображения не должен превышать 5 МБ.',

            'main_index.integer' => 'Некорректный индекс главной фотографии.',
            'main_index.min' => 'Индекс главной фотографии не может быть отрицательным.',
        ]);

        // Генерируем новый slug если заголовок изменился
        if ($post->title !== $validated['title']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $post->id);
        }

        DB::transaction(function () use ($post, $validated, $request) {
            $post->update($validated);

            // Обработка удаленных фотографий
            if ($request->filled('deleted_photos')) {
                $this->handleDeletedPhotos($request->get('deleted_photos'));
            }

            // Обработка новых загруженных фотографий
            if ($request->hasFile('photos')) {
                $this->handlePhotoUploads($post, $request->file('photos'), $request->get('main_index', 0));
            }

            // Обновление основной фотографии среди существующих
            if ($request->filled('main_photo_id')) {
                $this->updateMainPhotoById($post, $request->get('main_photo_id'));
            }

            // Обновление основной фотографии среди новых
            if ($request->filled('main_index')) {
                $this->updateMainPhoto($post, $request->get('main_index'));
            }
        });

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Пост успешно обновлен!');
    }

    /**
     * Удаление поста (полное удаление из базы данных)
     * 
     * @param string $slug
     * @return RedirectResponse
     */
    public function destroy(string $slug): RedirectResponse
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $this->authorize('delete', $post);

        // Удаляем фотографии из файловой системы
        foreach ($post->photos as $photo) {
            $fullPath = public_path($photo->photo_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        // Удаляем директорию с фотографиями поста
        $postDir = public_path('uploads/posts/' . $post->id);
        if (is_dir($postDir)) {
            rmdir($postDir);
        }

        // Удаляем пост полностью из базы данных (force delete)
        $post->forceDelete();

        return redirect()
            ->route('my-posts.index')
            ->with('success', 'Пост полностью удален из базы данных!');
    }

    /**
     * Генерация уникального slug для поста
     * 
     * @param string $title
     * @param int|null $excludeId ID поста для исключения из проверки
     * @return string
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        Log::info('Генерация slug', [
            'title' => $title,
            'initial_slug' => $slug,
            'exclude_id' => $excludeId
        ]);

        while (Post::withTrashed()->where('slug', $slug)
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            Log::info('Slug занят, пробуем новый', [
                'new_slug' => $slug,
                'counter' => $counter
            ]);
        }

        Log::info('Финальный slug', ['final_slug' => $slug]);
        return $slug;
    }

    /**
     * Обработка загрузки фотографий для поста
     * 
     * @param Post $post
     * @param array $photos
     * @param int|string $mainIndex
     * @return void
     */
    private function handlePhotoUploads(Post $post, array $photos, int|string $mainIndex = 0): void
    {
        foreach ($photos as $index => $photo) {
            try {
                // Сохраняем в public/uploads для прямого доступа
                $filename = time() . '_' . $index . '_' . $photo->getClientOriginalName();
                $path = 'uploads/posts/' . $post->id . '/' . $filename;

                // Создаем директорию если не существует
                $directory = public_path('uploads/posts/' . $post->id);
                if (!file_exists($directory)) {
                    if (!mkdir($directory, 0755, true)) {
                        throw new \Exception("Не удалось создать директорию: {$directory}");
                    }
                }

                // Перемещаем файл
                $photo->move($directory, $filename);

                $isMain = false;
                if (is_numeric($mainIndex) && $index === (int)$mainIndex) {
                    $isMain = true;
                } elseif (is_string($mainIndex) && str_starts_with($mainIndex, 'new_') && $index === (int)str_replace('new_', '', $mainIndex)) {
                    $isMain = true;
                }

                PostPhoto::create([
                    'post_id' => $post->id,
                    'photo_path' => $path,
                    'is_main' => $isMain,
                    'order' => $index,
                ]);
            } catch (\Exception $e) {
                Log::error('Ошибка загрузки фото: ' . $e->getMessage(), [
                    'post_id' => $post->id,
                    'photo_index' => $index,
                    'filename' => $photo->getClientOriginalName(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }
    }

    /**
     * Обработка удаленных фотографий
     * 
     * @param string $deletedPhotosIds
     * @return void
     */
    private function handleDeletedPhotos(string $deletedPhotosIds): void
    {
        $ids = array_filter(explode(',', $deletedPhotosIds));

        if (empty($ids)) {
            return;
        }

        $photos = PostPhoto::whereIn('id', $ids)->get();

        foreach ($photos as $photo) {
            // Удаляем файл из public/uploads
            $fullPath = public_path($photo->photo_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Удаляем запись из БД
            $photo->delete();
        }
    }

    /**
     * Обновление основной фотографии
     * 
     * @param Post $post
     * @param string $mainIndex
     * @return void
     */
    private function updateMainPhoto(Post $post, string $mainIndex): void
    {
        // Сбрасываем все фотографии как не основные
        $post->photos()->update(['is_main' => false]);

        if (is_numeric($mainIndex)) {
            // Устанавливаем существующую фотографию как основную
            $post->photos()->where('id', $mainIndex)->update(['is_main' => true]);
        } elseif (str_starts_with($mainIndex, 'new_')) {
            // Устанавливаем новую фотографию как основную
            $newIndex = (int)str_replace('new_', '', $mainIndex);
            $newPhotos = $post->photos()->whereNull('is_main')->orWhere('is_main', false)->orderBy('created_at', 'desc')->get();

            if (isset($newPhotos[$newIndex])) {
                $newPhotos[$newIndex]->update(['is_main' => true]);
            }
        }
    }

    /**
     * Обновление основной фотографии по ID
     * 
     * @param Post $post
     * @param string $photoId
     * @return void
     */
    private function updateMainPhotoById(Post $post, string $photoId): void
    {
        // Сбрасываем все фотографии как не основные
        $post->photos()->update(['is_main' => false]);

        // Устанавливаем выбранную фотографию как основную
        $post->photos()->where('id', $photoId)->update(['is_main' => true]);

        \Illuminate\Support\Facades\Log::info('Основная фотография изменена', [
            'post_id' => $post->id,
            'photo_id' => $photoId
        ]);
    }
}
