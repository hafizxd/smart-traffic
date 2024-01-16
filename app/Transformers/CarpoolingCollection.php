<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Constants\CarpoolingStatus;

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

        $resData['status_label'] = CarpoolingStatus::label($this->status);
        $resData['is_mine'] = $this->driver_id == Auth::user()->id;
        $resData['carpooling_passangers'] = CarpoolingPassangerCollection::collection($this->carpoolingPassangers);

        return $resData;
    }
}
