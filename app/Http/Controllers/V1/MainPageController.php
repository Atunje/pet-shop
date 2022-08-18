<?php

namespace App\Http\Controllers\V1;

use App\Models\Post;
use App\Models\Promotion;
use App\Http\Requests\V1\FilterRequest;
use App\Http\Resources\V1\PostResource;
use App\Http\Resources\V1\PromotionResource;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function promotions(FilterRequest $request)
    {
        $filter_params = $request->filterParams();
        $data = PromotionResource::collection(Promotion::getAll($filter_params))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function posts(FilterRequest $request)
    {
        $filter_params = $request->filterParams();
        $data = PostResource::collection(Post::getAll($filter_params))->resource;

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
