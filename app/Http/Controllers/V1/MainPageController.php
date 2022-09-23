<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\FilterRequest;
use App\Http\Resources\V1\PostResource;
use App\Http\Resources\V1\PromotionResource;
use App\Models\Post;
use App\Models\Promotion;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainPageController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/main/promotions",
     *      operationId="ListallPromotions",
     *      tags={"Main Page"},
     *      summary="Fetch promotions",
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="desc",
     *          in="query",
     *          @OA\Schema(
     *             type="boolean",
     *          ),
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Display a listing of promotions.
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
     * @OA\Get(
     *      path="/api/v1/main/blog",
     *      operationId="ListBlogPosts",
     *      tags={"Main Page"},
     *      summary="Fetch blog posts",
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="desc",
     *          in="query",
     *          @OA\Schema(
     *             type="boolean",
     *          ),
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
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
     * @OA\Get(
     *      path="/api/v1/main/blog/{uuid}",
     *      operationId="ListPromotions",
     *      tags={"Main Page"},
     *      summary="Fetch a promotions",
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Display the specified blog.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function showPost(Post $post)
    {
        return $this->jsonResponse(data: new PostResource($post));
    }
}
