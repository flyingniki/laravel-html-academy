<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Получение списка комментариев.
     */
    public function testGetFilmCommentsRoute()
    {
        $count = random_int(2, 10);

        $film = Film::factory()
            ->has(Comment::factory($count))
            ->create();

        $response = $this->getJson(route('comments.index', $film));

        $response->assertStatus(200);
        $response->assertJsonCount($count, 'data');
        $response->assertJsonFragment(['text' => $film->comments->first()->text]);
    }

    /**
     * Попытка добавления комментария гостем.
     */
    public function testAddFilmCommentByGuest()
    {
        $response = $this->postJson(route('comments.store', 1));

        $response->assertStatus(401);
    }

    /**
     * Проверка добавления комментария пользователем.
     */
    public function testAddFilmCommentByUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $film = Film::factory()->create();
        $comment = Comment::factory()->make();

        $response = $this->postJson(route('comments.store', $film), $comment->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('comments', [
            'film_id' => $film->id,
            'user_id' => $user->id,
            'text' => $comment->text,
            'rating' => $comment->rating,
        ]);
    }

    /**
     * Попытка редактирования комментария не аутентифицированным пользователем.
     */
    public function testUpdateCommentByGuest()
    {
        $comment = Comment::factory()->create();

        $response = $this->patchJson(route('comments.update', $comment), []);

        $response->assertStatus(401);
    }

    /**
     * Попытка редактирования комментария пользователем не автором комментария.
     */
    public function testUpdateCommentByCommonUser()
    {
        Sanctum::actingAs(User::factory()->create());

        $comment = Comment::factory()->create();

        $response = $this->patchJson(route('comments.update', $comment), []);

        $response->assertStatus(403);
    }

    /**
     * Успешное редактирование комментария автором.
     */
    public function testUpdateCommentByAthor()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $comment = Comment::factory()->for($user)->create();

        $data = [
            'text' => 'some text',
        ];

        $response = $this->patchJson(route('comments.update', $comment), $data);

        $response->assertStatus(200);
        $response->assertJsonFragment($data);
    }

    /**
     * Успешное редактирование комментария модератором.
     */
    public function testUpdateCommentByModerator()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $comment = Comment::factory()->create();

        $data = [
            'text' => 'some text',
        ];

        $response = $this->patchJson(route('comments.update', $comment), $data);

        $response->assertStatus(200);
        $response->assertJsonFragment($data);
    }

    /**
     * Проверка попытки удаления комментария не аутентифицированным пользователем.
     */
    public function testDeleteCommentByGuest()
    {
        $comment = Comment::factory()->create();

        $response = $this->deleteJson(route('comments.destroy', $comment->id));

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Запрос требует аутентификации.']);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    /**
     * Попытка удаления комментария пользователем не автором комментария.
     */
    public function testDeleteCommentByCommonUser()
    {
        Sanctum::actingAs(User::factory()->create());

        $comment = Comment::factory()->create();

        $response = $this->deleteJson(route('comments.destroy', $comment));

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Неавторизованное действие.']);
    }

    /**
     * Попытка удаления автором комментария имеющего ответы.
     */
    public function testDeleteCommentWithAnswersByAuthor()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $comment = Comment::factory()->for($user)->create();
        Comment::factory(3)->for($comment, 'parent')->create();

        $response = $this->deleteJson(route('comments.destroy', $comment));

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Неавторизованное действие.']);
    }

    /**
     * Успешное удаление автором комментария без ответов.
     */
    public function testDeleteCommentByAuthor()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $comment = Comment::factory()->for($user)->create();

        $response = $this->deleteJson(route('comments.destroy', $comment));

        $response->assertStatus(201);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /**
     * Успешное удаление модератором комментария и всех его ответов.
     */
    public function testDeleteCommentsByModerator()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $comment = Comment::factory()->create();
        Comment::factory(3)->for($comment, 'parent')->create();

        $response = $this->deleteJson(route('comments.destroy', $comment));

        $response->assertStatus(201);
        $this->assertDatabaseCount('comments', 0);
    }
}
