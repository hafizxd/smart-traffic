<?php

namespace App\Transformers;

use App\Transformers\UserCollection;
use Illuminate\Support\Facades\Storage;
use App\Constants\CarpoolingPassangerStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class CarpoolingPassangerCollection extends JsonResource
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
        $resData['passanger_data'] = new UserCollection($this->passanger);
        $resData['pick_type'] = ucwords(strtolower($this->pick_type));
        $resData['status'] = CarpoolingPassangerStatus::NEGOTIATE;
        $resData['passanger_data'] = new UserCollection($this->passanger);

        return $resData;
    }
}
