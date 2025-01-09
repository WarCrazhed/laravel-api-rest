<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Policies\RecipePolicy;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with('category', 'tags', 'user')->get();
        return RecipeResource::collection($recipes);
    }

    public function store(StoreRecipeRequest $request)
    {
        $recipe = $request->user()->recipes()->create($request->all());
        $recipe->tags()->attach(json_decode($request->tags));

        $recipe->image = $request->file('image')->store('recipes', 'public');
        $recipe->save();

        return response()->json(new RecipeResource($recipe), Response::HTTP_CREATED); // 201
    }

    public function show(Recipe $recipe)
    {
        $recipe = $recipe->load('category', 'tags', 'user');
        return new RecipeResource($recipe);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
        Gate::authorize('update', $recipe);
        $recipe->update($request->all());
        if ($tags = json_decode($request->tags)) {
            $recipe->tags()->sync($tags);
        }

        if ($request->file('image')) {
            $recipe->image = $request->file('image')->store('recipes', 'public');
            $recipe->save();
        }
        return response()->json(new RecipeResource($recipe), Response::HTTP_OK); // 200
    }

    public function destroy(Recipe $recipe)
    {
        Gate::authorize('delete', $recipe);
        $recipe->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT); // 204
    }
}
