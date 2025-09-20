<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Тесты для PostPolicy
 */
final class PostPolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private PostPolicy $policy;
    private User $admin;
    private User $user;
    private User $author;
    private Post $publishedPost;
    private Post $draftPost;
    private Post $moderationPost;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new PostPolicy();

        // Создаем пользователей
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->author = User::factory()->create(['role' => 'user']);

        // Создаем категорию
        $category = Category::factory()->create();

        // Создаем посты
        $this->publishedPost = Post::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $category->id,
            'status' => 'published'
        ]);

        $this->draftPost = Post::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $category->id,
            'status' => 'draft'
        ]);

        $this->moderationPost = Post::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $category->id,
            'status' => 'moderation'
        ]);
    }

    /**
     * Тест: viewAny - все пользователи могут просматривать список постов
     */
    public function test_view_any_allows_all_users(): void
    {
        $this->assertTrue($this->policy->viewAny($this->admin));
        $this->assertTrue($this->policy->viewAny($this->user));
        $this->assertTrue($this->policy->viewAny($this->author));
    }

    /**
     * Тест: view - опубликованные посты может просматривать любой пользователь
     */
    public function test_view_published_post_allows_all_users(): void
    {
        $this->assertTrue($this->policy->view($this->admin, $this->publishedPost));
        $this->assertTrue($this->policy->view($this->user, $this->publishedPost));
        $this->assertTrue($this->policy->view($this->author, $this->publishedPost));
    }

    /**
     * Тест: view - черновики может просматривать только автор и администратор
     */
    public function test_view_draft_post_restricts_access(): void
    {
        $this->assertTrue($this->policy->view($this->admin, $this->draftPost));
        $this->assertFalse($this->policy->view($this->user, $this->draftPost));
        $this->assertTrue($this->policy->view($this->author, $this->draftPost));
    }

    /**
     * Тест: view - посты на модерации может просматривать только автор и администратор
     */
    public function test_view_moderation_post_restricts_access(): void
    {
        $this->assertTrue($this->policy->view($this->admin, $this->moderationPost));
        $this->assertFalse($this->policy->view($this->user, $this->moderationPost));
        $this->assertTrue($this->policy->view($this->author, $this->moderationPost));
    }

    /**
     * Тест: create - все аутентифицированные пользователи могут создавать посты
     */
    public function test_create_allows_all_authenticated_users(): void
    {
        $this->assertTrue($this->policy->create($this->admin));
        $this->assertTrue($this->policy->create($this->user));
        $this->assertTrue($this->policy->create($this->author));
    }

    /**
     * Тест: update - только автор и администратор могут редактировать пост
     */
    public function test_update_restricts_to_author_and_admin(): void
    {
        // Администратор может редактировать любой пост
        $this->assertTrue($this->policy->update($this->admin, $this->publishedPost));
        $this->assertTrue($this->policy->update($this->admin, $this->draftPost));
        $this->assertTrue($this->policy->update($this->admin, $this->moderationPost));

        // Автор может редактировать свои посты
        $this->assertTrue($this->policy->update($this->author, $this->publishedPost));
        $this->assertTrue($this->policy->update($this->author, $this->draftPost));
        $this->assertTrue($this->policy->update($this->author, $this->moderationPost));

        // Обычный пользователь не может редактировать чужие посты
        $this->assertFalse($this->policy->update($this->user, $this->publishedPost));
        $this->assertFalse($this->policy->update($this->user, $this->draftPost));
        $this->assertFalse($this->policy->update($this->user, $this->moderationPost));
    }

    /**
     * Тест: delete - только автор и администратор могут удалять пост
     */
    public function test_delete_restricts_to_author_and_admin(): void
    {
        // Администратор может удалять любой пост
        $this->assertTrue($this->policy->delete($this->admin, $this->publishedPost));
        $this->assertTrue($this->policy->delete($this->admin, $this->draftPost));
        $this->assertTrue($this->policy->delete($this->admin, $this->moderationPost));

        // Автор может удалять свои посты
        $this->assertTrue($this->policy->delete($this->author, $this->publishedPost));
        $this->assertTrue($this->policy->delete($this->author, $this->draftPost));
        $this->assertTrue($this->policy->delete($this->author, $this->moderationPost));

        // Обычный пользователь не может удалять чужие посты
        $this->assertFalse($this->policy->delete($this->user, $this->publishedPost));
        $this->assertFalse($this->policy->delete($this->user, $this->draftPost));
        $this->assertFalse($this->policy->delete($this->user, $this->moderationPost));
    }

    /**
     * Тест: restore - только администратор может восстанавливать посты
     */
    public function test_restore_restricts_to_admin_only(): void
    {
        $this->assertTrue($this->policy->restore($this->admin, $this->publishedPost));
        $this->assertFalse($this->policy->restore($this->user, $this->publishedPost));
        $this->assertFalse($this->policy->restore($this->author, $this->publishedPost));
    }

    /**
     * Тест: forceDelete - только администратор может окончательно удалять посты
     */
    public function test_force_delete_restricts_to_admin_only(): void
    {
        $this->assertTrue($this->policy->forceDelete($this->admin, $this->publishedPost));
        $this->assertFalse($this->policy->forceDelete($this->user, $this->publishedPost));
        $this->assertFalse($this->policy->forceDelete($this->author, $this->publishedPost));
    }

    /**
     * Тест: проверка прав для постов разных авторов
     */
    public function test_permissions_for_different_authors(): void
    {
        $anotherAuthor = User::factory()->create(['role' => 'user']);
        $anotherPost = Post::factory()->create([
            'user_id' => $anotherAuthor->id,
            'status' => 'published'
        ]);

        // Автор не может редактировать/удалять чужие посты
        $this->assertFalse($this->policy->update($this->author, $anotherPost));
        $this->assertFalse($this->policy->delete($this->author, $anotherPost));

        // Но может просматривать опубликованные
        $this->assertTrue($this->policy->view($this->author, $anotherPost));

        // Администратор может все
        $this->assertTrue($this->policy->view($this->admin, $anotherPost));
        $this->assertTrue($this->policy->update($this->admin, $anotherPost));
        $this->assertTrue($this->policy->delete($this->admin, $anotherPost));
    }

    /**
     * Тест: проверка статусов постов для просмотра
     */
    public function test_view_permissions_by_post_status(): void
    {
        $testUser = User::factory()->create(['role' => 'user']);

        // Опубликованные посты - все могут просматривать
        $this->assertTrue($this->policy->view($testUser, $this->publishedPost));

        // Черновики и модерация - только автор и админ
        $this->assertFalse($this->policy->view($testUser, $this->draftPost));
        $this->assertFalse($this->policy->view($testUser, $this->moderationPost));

        // Автор может просматривать свои посты в любом статусе
        $this->assertTrue($this->policy->view($this->author, $this->publishedPost));
        $this->assertTrue($this->policy->view($this->author, $this->draftPost));
        $this->assertTrue($this->policy->view($this->author, $this->moderationPost));

        // Админ может просматривать любые посты
        $this->assertTrue($this->policy->view($this->admin, $this->publishedPost));
        $this->assertTrue($this->policy->view($this->admin, $this->draftPost));
        $this->assertTrue($this->policy->view($this->admin, $this->moderationPost));
    }
}
