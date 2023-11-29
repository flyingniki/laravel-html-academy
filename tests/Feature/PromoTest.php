<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PromoTest extends TestCase
{
    use RefreshDatabase;

    public function testGetPromoRoute()
    {
        $film1 = Film::factory()->create(['promo' => true, 'updated_at' => now()]);

        Film::factory()->create(['promo' => true, 'updated_at' => now()->subDay()]);

        $response = $this->getJson(route('promo.show'));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $film1->id]);
    }

    /**
     * Кладем в кеш заведомо не верный фильм, что бы убедиться что результат возвращается из кеша.
     */
    public function testGetFromCache()
    {
        Film::factory()->create(['promo' => true, 'updated_at' => now()]);
        $cached = Film::factory()->create(['promo' => false, 'updated_at' => now()]);
        cache()->forever(Film::CACHE_PROMO_KEY, $cached);

        $response = $this->getJson(route('promo.show'));

        $response->assertJsonFragment(['id' => $cached->id]);
    }

    /**
     * Проверка попытки изменения флага promo пользователем не модератором.
     * Ожидается ошибка авторизации, и не внесение изменений в БД.
     */
    public function testAddPromoByCommonUser()
    {
        Sanctum::actingAs(User::factory()->create());

        $film = Film::factory()->create(['promo' => false]);

        $response = $this->postJson(route('promo.store', $film), ['promo' => true]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('films', [
            'id' => $film->id,
            'promo' => true,
        ]);
    }

    /**
     * Установка флага promo модератором.
     */
    public function testAddPromoRoute()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $film = Film::factory()->create(['promo' => false]);

        $response = $this->postJson(route('promo.store', $film), ['promo' => true]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'promo' => true,
        ]);
    }

    /**
     * Снятие флага promo модератором.
     */
    public function testRemovePromo()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $film = Film::factory()->create(['promo' => true]);

        $response = $this->postJson(route('promo.store', $film), ['promo' => false]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'promo' => false,
        ]);
    }

    /**
     * Проверка очистки кеша, при обновлении записи о промо фильме.
     */
    public function testFlushCacheOnUpdate()
    {
        cache()->forever(Film::CACHE_PROMO_KEY, 'some value');

        Sanctum::actingAs(User::factory()->moderator()->create());

        $film = Film::factory()->create(['promo' => false]);

        $this->postJson(route('promo.store', $film), ['promo' => true]);

        $this->assertNull(cache()->get(Film::CACHE_PROMO_KEY));
    }
}
