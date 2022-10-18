<?php

namespace App\Http\Controllers\Backend;


use App\Exceptions\ApiException;
use App\Http\Resources\ApiResource;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\User;
use App\Services\OssService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Class OssController
 * @package App\Http\Controllers\Backend
 */
class UploadController extends BackendController
{

    /**
     * @param Request $request
     * @return FileResource
     * @throws ApiException
     */
    public function single(Request $request): FileResource
    {
        $file = $this->singleUpload($request->file('file'));

        return FileResource::make($file);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws ApiException
     */
    public function multiple(Request $request): AnonymousResourceCollection
    {
        $files = $this->multipleUpload($request);

        return FileResource::collection($files);
    }

    /**
     * wangEditor 编辑器上传
     * @param Request $request
     * @return \Illuminate\Config\Repository|mixed
     */
    public function wangEditor(Request $request)
    {
        $response = $this->upload($request);
        $files = $response->pluck('url');

        return Response::json(['data' => $files, 'errno' => 0]);
    }

    /**
     * @return mixed
     */
    public function ossGetSign()
    {
        $dir = 'uploads/' . date('Y-m-d');
        $callbackUrl = 'https://api.runhub.cn/v1/oss/callback';
        $signs = app(OssService::class)->getSign($dir, $callbackUrl);

        return ApiResource::make($signs);
    }

    /**
     * 单文件上传
     * @param UploadedFile $file
     * @return File
     * @throws ApiException
     */
    protected function singleUpload(UploadedFile $file): File
    {
        if (!$file->isValid()) {
            throw new ApiException('文件上传异常');
        }

        $url = Storage::url($file->store('public'));
        $fileModel = new File();
        $fileModel->user_id = !empty($this->user) ? $this->user->id : 0;
        $fileModel->name = $file->getClientOriginalName();
        $fileModel->ext = $file->getClientOriginalExtension();
        $fileModel->mime = $file->getClientMimeType();
        $fileModel->url = $url;
        $fileModel->size = $file->getSize();
        $fileModel->md5 = md5_file($file->getRealPath());
        $fileModel->sha1 = sha1_file($file->getRealPath());
        $fileModel->save();

        return $fileModel;
    }

    /**
     * 多文件上传
     * @param Request $request
     * @return Collection
     * @throws ApiException
     */
    protected function multipleUpload(Request $request): Collection
    {
        $uploadFiles = $request->allFiles();
        $files = collect([]);

        foreach ($uploadFiles as $uploadFile) {
            $files->push($this->singleUpload($uploadFile));
        }

        return $files;
    }
}


