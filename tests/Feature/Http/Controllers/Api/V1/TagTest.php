<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TagTest extends TestCase
{
    public function testIndex(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $tags = Tag::factory(2)->create();
        $response = $this->getJson('/api/v1/tags');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'type',
                        'attributes' => ['name'],
                        'relationships' => [
                            'recipes' => [],
                        ],
                    ]
                ],
            ]);
    }

    public function testShow(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $tag = Tag::factory()->create();

        $response = $this->getJson('/api/v1/tags/' . $tag->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attributes' => ['name'],
                    'relationships' => [
                        'recipes' => [],
                    ],
                ],
            ]);
    }
}
