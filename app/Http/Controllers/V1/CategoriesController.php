<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        $per_pg = $request->has('limit') ? intval($request->limit) : 10;
        $data = CategoryResource::collection(Category::getAll($request->all(), $per_pg))->resource;

        return response()->json(['success' => 1, 'data' => $data]);
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
        return response()->json(['success' => 1, 'data' => new CategoryResource($category)]);
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category)
    {
        return response()->json(['success' => 1, 'data' => new CategoryResource($category)]);
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
            return response()->json(['success' => 1, 'data' => $category]);
        }

        return response()->json(['success' => 0], Response::HTTP_UNPROCESSABLE_ENTITY);
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
            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
