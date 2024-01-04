<?php

namespace App\Transformers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $resData = $this->resource->toArray();

        $resData['image_url'] = null;
        if (isset($this->image)) {
            $resData['image_url'] = Storage::url('documents/' . $this->image);
        }

        return $resData;
    }
}
