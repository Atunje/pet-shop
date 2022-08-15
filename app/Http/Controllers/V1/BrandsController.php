<?php

namespace App\Http\Controllers\V1;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\V1\BrandRequest;
use App\Http\Resources\V1\BrandResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $per_pg = $request->has('limit') ? intval($request->limit) : 10;
        $data = BrandResource::collection(Brand::getAll($request->all(), $per_pg))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BrandRequest $request
     * @return JsonResponse
     */
    public function store(BrandRequest $request)
    {
        $inputs = $request->all();
        $inputs['slug'] = Str::slug(strval($request->title));

        $brand = Brand::create($inputs);
        return $this->jsonResponse(data:new BrandResource($brand));
    }

    /**
     * Display the specified resource.
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function show(Brand $brand)
    {
        return $this->jsonResponse(data: new BrandResource($brand));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BrandRequest $request
     * @param Brand $brand
     * @return JsonResponse
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        $inputs = $request->all();
        $inputs['slug'] = Str::slug(strval($request->title));

        if ($brand->update($inputs)) {
            return $this->jsonResponse(data: $brand);
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function destroy(Brand $brand)
    {
        if ($brand->delete()) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
