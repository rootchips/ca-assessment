<?php

namespace App\Http\Controllers;

use App\Contracts\UploadRepositoryContract;
use App\Http\Requests\UploadStoreRequest;
use Illuminate\Support\Facades\Redis;
use App\Http\Resources\UploadResource;
use App\Support\ApiResponse;
use Throwable;

class UploadController extends Controller
{
    /**
     * Create a new controller instance.
        *
        * @return void
     */
    public function __construct(private UploadRepositoryContract $uploads) {}

    /**
     * Display all uploads.
        *
        * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return ApiResponse::success(
            'Uploads fetched successfully.',
            UploadResource::collection($this->uploads->all())
        );
    }

    /**
     * Store an uploaded file for processing.
        *
        * @param UploadStoreRequest $request
        * @return \Illuminate\Http\JsonResponse
     */
    public function store(UploadStoreRequest $request)
    {
        $file = $request->file('file');

        $upload = $this->uploads->create($file);

        return ApiResponse::success('Upload accepted for processing.', new UploadResource($upload), 201);
    }

    /**
     * Return current upload progress by upload ID.
        *
        * @param string $id
        * @return \Illuminate\Http\JsonResponse
     */
    public function progress(string $id)
    {
        try {
            $progress = (int) Redis::get("upload:progress:$id");
        } catch (Throwable) {
            $progress = 0;
        }

        return ApiResponse::success('Upload progress fetched successfully.', [
            'progress' => $progress,
        ]);
    }
}
