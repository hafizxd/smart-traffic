<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CarpoolingCollection extends JsonResource
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

        $resData['is_mine'] = $this->driver_id == $this->auth('api')->user()->id;
        $resData['carpooling_passangers'] = CarpoolingPassangerCollection::collection($this->carpoolingPassangers);

        return $resData;
    }
}
