<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class RecipeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testIndex(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Category::factory()->create();

        $recipes = Recipe::factory(2)->create();

        $response = $this->getJson('/api/v1/recipes');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'type',
                        'attributes' => ['title', 'description'],
                    ]
                ],
            ]);
    }

    public function testStore(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'category_id' =>    $category->id,
            'title' =>          $this->faker->sentence,
            'description' =>    $this->faker->paragraph,
            'ingredients' =>    $this->faker->text,
            'instructions' =>   $this->faker->text,
            'tags' =>           $tag->id,
            'image' =>          UploadedFile::fake()->image('recipe.jpg'),
        ];

        $response = $this->postJson('/api/v1/recipes/', $data);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testShow(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Category::factory()->create();

        $recipe = Recipe::factory()->create();

        $response = $this->getJson('/api/v1/recipes/' . $recipe->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attributes' => ['title', 'description'],
                ],
            ]);
    }

    public function testUpdate(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::factory()->create();
        $recipe = Recipe::factory()->create();

        $data = [
            'category_id' =>    $category->id,
            'title' =>          'updated title',
            'description' =>    'updated description',
            'ingredients' =>    $this->faker->text,
            'instructions' =>   $this->faker->text,
        ];

        $response = $this->putJson('/api/v1/recipes/'. $recipe->id, $data);
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'title' => 'updated title',
            'description' => 'updated description',
        ]);
    }

    public function testDestroy(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Category::factory()->create();

        $recipe = Recipe::factory()->create();

        $response = $this->deleteJson('/api/v1/recipes/' . $recipe->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
