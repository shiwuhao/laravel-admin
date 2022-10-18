<?php

namespace App\Http\Controllers\Backend;


use App\Http\Resources\ApiResource;
use App\Http\Resources\FileResource;
use App\Services\OssService;
use Illuminate\Http\Request;
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function normal(Request $request)
    {
        $response = $this->upload($request);

        return FileResource::collection($response);
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
     * 上传方法
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    protected function upload(Request $request)
    {
        $response = collect([]);
        $files = collect($request->allFiles())->flatten()->toArray();
        foreach ($files as $file) {
            //判断文件是否上传成功
            if ($file->isValid()) {
                $originalName = $file->getClientOriginalName();//获取原文件名
                $ext = $file->getClientOriginalExtension();//扩展名
                $type = $file->getClientMimeType();//文件类型
                $size = $file->getSize();// 文件大小

                $url = Storage::url(Storage::put('/public', $file));

                $file = new \App\Models\File([
                    'user_id' => !empty($this->user) ? $this->user->id : 0,
                    'name' => $originalName,
                    'ext' => $ext,
                    'mime' => $type,
                    'url' => $url,
                    'size' => $size,
                    'md5' => md5_file($file->getRealPath()),
                    'sha1' => sha1_file($file->getRealPath()),
                ]);
                $file->save();

                $response->push($file);
            }
        }
        return $response;
    }

    /**
     * @return mixed
     */
    public function sign()
    {
        $dir = 'uploads/' . date('Y-m-d');
        $callbackUrl = url('api/oss/callback', [], true);
        $signs = app(OssService::class)->getSign($dir, $callbackUrl);

        return ApiResource::make($signs);
    }

}


