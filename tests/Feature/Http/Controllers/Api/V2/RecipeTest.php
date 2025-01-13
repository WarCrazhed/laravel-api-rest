<?php

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexV2(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Category::factory()->create();

        $recipes = Recipe::factory(5)->create();

        $response = $this->getJson('/api/v2/recipes');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' =>   [],
                'links' =>  [],
                'meta' =>   [],
            ]);
    }
}
