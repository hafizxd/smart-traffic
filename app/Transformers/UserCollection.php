<?php

namespace App\Transformers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCollection extends JsonResource
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

        $resData['profile_picture_url'] = null;
        if (isset($this->profile_picture)) {
            $resData['profile_picture_url'] = Storage::url('users/' . $this->profile_picture);
        }

        return $resData;
    }
}
