<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Контроллер для управления категориями
 * 
 * Обрабатывает CRUD операции для категорий: создание, просмотр,
 * редактирование, удаление с применением политик доступа.
 */
final class CategoryController extends Controller
{
    public function __construct()
    {
        // CRUD операции доступны только администраторам
        $this->middleware(['auth', 'admin'])->except(['index', 'show']);
    }

    /**
     * Отображение списка категорий
     * 
     * @return View
     */
    public function index(): View
    {
        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Отображение формы создания категории
     * 
     * @return View
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Сохранение новой категории
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Название категории обязательно для заполнения.',
            'name.unique' => 'Категория с таким названием уже существует.',
            'name.max' => 'Название категории не может содержать более 255 символов.',
            'description.max' => 'Описание не может содержать более 1000 символов.',
        ]);

        $validated['slug'] = $this->generateUniqueSlug($validated['name']);

        $category = Category::create($validated);

        return redirect()
            ->route('categories.show', $category->slug)
            ->with('success', 'Категория успешно создана!');
    }

    /**
     * Отображение категории с постами
     * 
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = $category->posts()
            ->where('status', 'published')
            ->with(['user', 'photos'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('categories.show', compact('category', 'posts'));
    }

    /**
     * Отображение формы редактирования категории
     * 
     * @param string $slug
     * @return View
     */
    public function edit(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        return view('categories.edit', compact('category'));
    }

    /**
     * Обновление категории
     * 
     * @param Request $request
     * @param string $slug
     * @return RedirectResponse
     */
    public function update(Request $request, string $slug): RedirectResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Название категории обязательно для заполнения.',
            'name.unique' => 'Категория с таким названием уже существует.',
            'name.max' => 'Название категории не может содержать более 255 символов.',
            'description.max' => 'Описание не может содержать более 1000 символов.',
        ]);

        // Генерируем новый slug если название изменилось
        if ($category->name !== $validated['name']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $category->id);
        }

        $category->update($validated);

        return redirect()
            ->route('categories.show', $category->slug)
            ->with('success', 'Категория успешно обновлена!');
    }

    /**
     * Удаление категории
     * 
     * @param string $slug
     * @return RedirectResponse
     */
    public function destroy(string $slug): RedirectResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // Проверяем, есть ли посты в этой категории
        if ($category->posts()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Нельзя удалить категорию, в которой есть посты. Сначала переместите или удалите все посты из этой категории.');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Категория успешно удалена!');
    }

    /**
     * Генерация уникального slug для категории
     * 
     * @param string $name
     * @param int|null $excludeId ID категории для исключения из проверки
     * @return string
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
