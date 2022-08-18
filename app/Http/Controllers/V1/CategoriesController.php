<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Http\Requests\V1\FilterRequest;
use App\Http\Requests\V1\CategoryRequest;
use App\Http\Resources\V1\CategoryResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoriesController extends Controller
{
    /**
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
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        $inputs = $request->all();
        $inputs['slug'] = Str::slug(strval($request->title));

        $category = Category::create($inputs);
        return $this->jsonResponse(data: new CategoryResource($category));
    }

    /**
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
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $inputs = $request->all();
        $inputs['slug'] = Str::slug(strval($request->title));

        if ($category->update($inputs)) {
            return $this->jsonResponse(data: $category);
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Remove the specified resource from storage.
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
