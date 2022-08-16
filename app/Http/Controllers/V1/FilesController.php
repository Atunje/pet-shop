<?php

namespace App\Http\Controllers\V1;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use App\Http\Requests\V1\FileRequest;
use App\Http\Resources\V1\FileResource;
use Symfony\Component\HttpFoundation\Response;

class FilesController extends Controller
{
    /**
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
        $filename = Str::random(40) . "." . $uploaded_file->getClientOriginalExtension();

        $file = $uploaded_file->move($path, $filename);

        $record = new File([
            'name' => Str::random(16),
            'type' => $file->getMimeType(),
            'size' => round($file->getSize()/1024) . " KB",
            'path' => 'public/pet-shop/' . $file->getFilename()
        ]);

        if ($record->save()) {
            return $this->jsonResponse(data: new FileResource($record));
        } else {
            //delete the file
            unlink($file);
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $per_pg = $request->has('limit') ? intval($request->limit) : 10;
        $data = FileResource::collection(File::getAll($request->all(), $per_pg))->resource;

        return $this->jsonResponse(data:$data);
    }
}
