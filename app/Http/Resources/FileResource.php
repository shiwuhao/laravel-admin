<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ext' => $this->ext,
            'url' => config('app.url').$this->url,
            'size' => $this->size,
            'md5' => $this->md5,
            'sha1' => $this->sha1,
            'mime' => $this->mime,
            'relativeUrl' => $this->url,
//            'thumbnailUrl' => getThumbnail($this->url, 'thumb200'),
        ];
    }
}
