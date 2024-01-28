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
    public function toArray($request, $withCarpooling = false)
    {
        $resData = $this->resource->toArray();
        $resData['status_label'] = CarpoolingPassangerStatus::label($this->status);
        $resData['pick_type'] = ucwords(strtolower($this->pick_type));
        $resData['passanger_data'] = new UserCollection($this->passanger);

        if ($withCarpooling)
            $resData['carpooling_data'] = new CarpoolingCollection($this->carpooling);

        return $resData;
    }
}
