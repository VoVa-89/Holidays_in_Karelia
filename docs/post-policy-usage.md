# PostPolicy - Политика доступа для постов

## Обзор

`PostPolicy` определяет права пользователей на выполнение различных действий с постами в системе "Отдых в Карелии".

## Методы политики

### 1. `viewAny(User $user): bool`
**Назначение:** Определяет, может ли пользователь просматривать список постов.

**Правила:**
- ✅ **Все пользователи** могут просматривать список постов

**Использование:**
```php
// В контроллере
$this->authorize('viewAny', Post::class);

// В Blade
@can('viewAny', App\Models\Post::class)
    <a href="{{ route('posts.index') }}">Все посты</a>
@endcan
```

### 2. `view(User $user, Post $post): bool`
**Назначение:** Определяет, может ли пользователь просматривать конкретный пост.

**Правила:**
- ✅ **Опубликованные посты** - все могут просматривать
- ✅ **Черновики** - только автор и администратор
- ✅ **Посты на модерации** - только автор и администратор

**Использование:**
```php
// В контроллере
$this->authorize('view', $post);

// В Blade
@can('view', $post)
    <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
@endcan
```

### 3. `create(User $user): bool`
**Назначение:** Определяет, может ли пользователь создавать новые посты.

**Правила:**
- ✅ **Все аутентифицированные пользователи** могут создавать посты

**Использование:**
```php
// В контроллере
$this->authorize('create', Post::class);

// В Blade
@can('create', App\Models\Post::class)
    <a href="{{ route('posts.create') }}">Создать пост</a>
@endcan
```

### 4. `update(User $user, Post $post): bool`
**Назначение:** Определяет, может ли пользователь редактировать пост.

**Правила:**
- ✅ **Автор поста** может редактировать свои посты
- ✅ **Администратор** может редактировать любые посты
- ❌ **Обычные пользователи** не могут редактировать чужие посты

**Использование:**
```php
// В контроллере
$this->authorize('update', $post);

// В Blade
@can('update', $post)
    <a href="{{ route('posts.edit', $post->slug) }}">Редактировать</a>
@endcan
```

### 5. `delete(User $user, Post $post): bool`
**Назначение:** Определяет, может ли пользователь удалять пост.

**Правила:**
- ✅ **Автор поста** может удалять свои посты
- ✅ **Администратор** может удалять любые посты
- ❌ **Обычные пользователи** не могут удалять чужие посты

**Использование:**
```php
// В контроллере
$this->authorize('delete', $post);

// В Blade
@can('delete', $post)
    <form action="{{ route('posts.destroy', $post->slug) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Удалить</button>
    </form>
@endcan
```

### 6. `restore(User $user, Post $post): bool`
**Назначение:** Определяет, может ли пользователь восстанавливать удаленные посты.

**Правила:**
- ✅ **Только администраторы** могут восстанавливать посты

**Использование:**
```php
// В контроллере
$this->authorize('restore', $post);

// В Blade
@can('restore', $post)
    <a href="{{ route('posts.restore', $post->id) }}">Восстановить</a>
@endcan
```

### 7. `forceDelete(User $user, Post $post): bool`
**Назначение:** Определяет, может ли пользователь окончательно удалять посты.

**Правила:**
- ✅ **Только администраторы** могут окончательно удалять посты

**Использование:**
```php
// В контроллере
$this->authorize('forceDelete', $post);

// В Blade
@can('forceDelete', $post)
    <a href="{{ route('posts.force-delete', $post->id) }}">Окончательно удалить</a>
@endcan
```

## Статусы постов и права доступа

| Статус | Просмотр | Редактирование | Удаление |
|--------|----------|----------------|----------|
| **published** | Все пользователи | Автор + Админ | Автор + Админ |
| **draft** | Автор + Админ | Автор + Админ | Автор + Админ |
| **moderation** | Автор + Админ | Автор + Админ | Автор + Админ |

## Роли пользователей

### Администратор (`role = 'admin'`)
- ✅ Может просматривать любые посты
- ✅ Может редактировать любые посты
- ✅ Может удалять любые посты
- ✅ Может восстанавливать посты
- ✅ Может окончательно удалять посты

### Автор поста
- ✅ Может просматривать свои посты в любом статусе
- ✅ Может просматривать опубликованные чужие посты
- ✅ Может редактировать только свои посты
- ✅ Может удалять только свои посты
- ❌ Не может восстанавливать посты
- ❌ Не может окончательно удалять посты

### Обычный пользователь
- ✅ Может просматривать опубликованные посты
- ✅ Может создавать новые посты
- ❌ Не может просматривать черновики и посты на модерации
- ❌ Не может редактировать чужие посты
- ❌ Не может удалять чужие посты
- ❌ Не может восстанавливать посты
- ❌ Не может окончательно удалять посты

## Примеры использования в контроллерах

```php
class PostController extends Controller
{
    public function index()
    {
        // Проверка не требуется - viewAny разрешает всем
        $posts = Post::where('status', 'published')->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        // Проверяем права на просмотр конкретного поста
        $this->authorize('view', $post);
        
        $post->incrementViews();
        return view('posts.show', compact('post'));
    }

    public function create()
    {
        // Проверяем права на создание постов
        $this->authorize('create', Post::class);
        
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Проверяем права на создание постов
        $this->authorize('create', Post::class);
        
        // Создание поста...
    }

    public function edit(Post $post)
    {
        // Проверяем права на редактирование
        $this->authorize('update', $post);
        
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        // Проверяем права на редактирование
        $this->authorize('update', $post);
        
        // Обновление поста...
    }

    public function destroy(Post $post)
    {
        // Проверяем права на удаление
        $this->authorize('delete', $post);
        
        // Удаление поста...
    }
}
```

## Примеры использования в Blade шаблонах

```blade
{{-- Проверка прав на создание --}}
@can('create', App\Models\Post::class)
    <a href="{{ route('posts.create') }}" class="btn btn-primary">
        Создать пост
    </a>
@endcan

{{-- Проверка прав на просмотр --}}
@can('view', $post)
    <h2>{{ $post->title }}</h2>
    <p>{{ $post->description }}</p>
@else
    <p>У вас нет прав для просмотра этого поста.</p>
@endcan

{{-- Проверка прав на редактирование --}}
@can('update', $post)
    <a href="{{ route('posts.edit', $post->slug) }}" class="btn btn-secondary">
        Редактировать
    </a>
@endcan

{{-- Проверка прав на удаление --}}
@can('delete', $post)
    <form action="{{ route('posts.destroy', $post->slug) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" 
                onclick="return confirm('Вы уверены?')">
            Удалить
        </button>
    </form>
@endcan

{{-- Проверка прав администратора --}}
@can('restore', $post)
    <a href="{{ route('posts.restore', $post->id) }}" class="btn btn-warning">
        Восстановить
    </a>
@endcan
```

## Регистрация политики

Политика зарегистрирована в `app/Providers/AuthServiceProvider.php`:

```php
protected $policies = [
    Post::class => PostPolicy::class,
];
```

## Тестирование

Созданы комплексные тесты в `tests/Feature/PostPolicyTest.php`, покрывающие все сценарии использования политики.

## Безопасность

- ✅ **Автоматическая проверка** прав в контроллерах
- ✅ **Защита от несанкционированного доступа** к постам
- ✅ **Разделение прав** по ролям пользователей
- ✅ **Контроль статусов** постов для просмотра
- ✅ **Защита авторских прав** на контент

