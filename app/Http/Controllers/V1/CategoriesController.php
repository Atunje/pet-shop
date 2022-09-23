<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\CategoryRequest;
use App\Http\Requests\V1\FilterRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/categories",
     *      operationId="categoryListing",
     *      tags={"Categories"},
     *      summary="List of categories",
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
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(FilterRequest $request)
    {
        $filter_params = $request->filterParams();
        $data = CategoryResource::collection(Category::getAll($filter_params))->resource;

        return $this->jsonResponse(data: $data);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/category/create",
     *      operationId="CreateCategory",
     *      tags={"Categories"},
     *      summary="Create a new category",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={
     *                      "title",
     *                  },
     *                  @OA\Property(property="title", type="string"),
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        $inputs = $request->validFields();
        $inputs['slug'] = Str::slug(strval($request->title));

        $category = Category::create($inputs);

        return $this->jsonResponse(data: new CategoryResource($category));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/category/{uuid}",
     *      operationId="showCategory",
     *      tags={"Categories"},
     *      summary="Fetch a category",
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
     * Display the specified resource.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category)
    {
        return $this->jsonResponse(data: new CategoryResource($category));
    }

    /**
     * @OA\Put(
     *      path="/api/v1/category/{uuid}",
     *      operationId="UpdateCategory",
     *      tags={"Categories"},
     *      summary="Update an existing category",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={
     *                      "title",
     *                  },
     *                  @OA\Property(property="title", type="string"),
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $inputs = $request->validFields();
        $inputs['slug'] = Str::slug(strval($request->title));

        if ($category->update($inputs)) {
            return $this->jsonResponse(data: new CategoryResource($category));
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/category/{uuid}",
     *      operationId="deleteCategory",
     *      tags={"Categories"},
     *      summary="Delete a category",
     *      security={{"bearerAuth":{}}},
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
     * Remove the specified category from storage.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        if ($category->delete()) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
