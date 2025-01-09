<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $categories = Category::factory(2)->create();
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'type',
                        'attributes' => ['name'],
                    ]
                ],
            ]);
    }

    public function testShow(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::factory()->create();

        $response = $this->getJson('/api/v1/categories/' . $category->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attributes' => ['name'],
                ],
            ]);
    }
}
