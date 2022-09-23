<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\FileRequest;
use App\Http\Resources\V1\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class FilesController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/file/upload",
     *      operationId="CreateFile",
     *      tags={"Files"},
     *      summary="Upload a file",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={
     *                      "file",
     *                  },
     *                  @OA\Property(property="file", type="file"),
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
     * Upload file
     *
     * @param FileRequest $request
     * @return JsonResponse
     */
    public function store(FileRequest $request): JsonResponse
    {
        /** @var UploadedFile $uploaded_file */
        $uploaded_file = $request->file('file');

        $path = public_path('pet-shop');
        $filename = Str::random(40).'.'.$uploaded_file->getClientOriginalExtension();

        $file = $uploaded_file->move($path, $filename);

        $record = new File([
            'name' => Str::random(16),
            'type' => $file->getMimeType(),
            'size' => round($file->getSize() / 1024).' KB',
            'path' => $file->getPath(),
        ]);

        if ($record->save()) {
            return $this->jsonResponse(data: new FileResource($record));
        }

        //delete the file
        unlink($file);

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/file/{uuid}",
     *      operationId="showFile",
     *      tags={"Files"},
     *      summary="Fetch a file",
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
     * Display the specified file
     *
     * @param File $file
     * @return JsonResponse
     */
    public function show(File $file)
    {
        return $this->jsonResponse(data: new FileResource($file));
    }
}
