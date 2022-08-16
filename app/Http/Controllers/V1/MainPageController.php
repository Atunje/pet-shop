<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\V1\PromotionResource;
use App\Models\Post;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Http\Resources\V1\PostResource;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function promotions(Request $request)
    {
        $per_pg = $request->has('limit') ? intval($request->limit) : 10;
        $data = PromotionResource::collection(Promotion::getAll($request->all(), $per_pg))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function posts(Request $request)
    {
        $per_pg = $request->has('limit') ? intval($request->limit) : 10;
        $data = PostResource::collection(Post::getAll($request->all(), $per_pg))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function showPost(Post $post)
    {
        return $this->jsonResponse(data: new PostResource($post));
    }
}
