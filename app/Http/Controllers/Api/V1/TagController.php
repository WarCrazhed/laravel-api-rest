<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::with('recipes.category', 'recipes.tags', 'recipes.user')->get();
        return TagResource::collection($tags);
    }

    public function show(Tag $tag)
    {
        $tag = $tag->load('recipes.category', 'recipes.tags', 'recipes.user');
        return new TagResource($tag);
    }
}
