<?php

namespace App\Transformers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleCollection extends JsonResource
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

        $resData['vehicle_images'] = [];
        if (!empty($this->vehicleImages)) {
            foreach ($this->vehicleImages as $image) {
                $resData['vehicle_images'][] = [
                    'id' => $image->id,
                    'image' => $image->image,
                    'image_url' => Storage::url('vehicle_images/' . $image->image)
                ];
            }
        }

        return $resData;
    }
}
