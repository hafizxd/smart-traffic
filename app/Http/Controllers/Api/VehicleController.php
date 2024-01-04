<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Transformers\VehicleCollection;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Auth::user()->vehicles()->with('vehicleImages')->get();

        return composeReply(true, 'Success', VehicleCollection::collection($vehicles));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'capacity' => 'required|numeric',
            'vehicle_images.*.image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $vehicle = Auth::user()->vehicles()->create([
                'name' => $request->name,
                'capacity' => $request->capacity,
            ]);

            if (isset($request->vehicle_images) && count($request->vehicle_images) > 0) {
                $imagesData = [];
                foreach ($request->vehicle_images as $image) {
                    $fileName = time() . '_' . $image['image']->getClientOriginalName();
                    $image['image']->storeAs('vehicle_images', $fileName, 'public');

                    $imagesData[] = ['image' => $fileName];
                }

                $vehicle->vehicleImages()->createMany($imagesData);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return composeReply(false, 'Error system' . [
                'errors' => $th->getMessage()
            ], 500);
        }

        return composeReply(true, 'Success', new VehicleCollection($vehicle));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'capacity' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle = Auth::user()->vehicles()->findOrFail($id);
        $vehicle->update([
            'name' => $request->name,
            'capacity' => $request->capacity
        ]);

        return composeReply(true, 'Success', new VehicleCollection($vehicle->refresh()));
    }

    public function delete(Request $request, $id)
    {
        $vehicle = Auth::user()->vehicles()->findOrFail($id);
        $vehicle->delete();

        return composeReply(true, 'Success', new VehicleCollection($vehicle));
    }

    public function storeImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle = Auth::user()->vehicles()->findOrFail($id);

        $fileName = time() . '_' . $request->image->getClientOriginalName();
        $request->image->storeAs('vehicle_images', $fileName, 'public');
        $vehicle->vehicleImages()->create([
            'image' => $fileName
        ]);

        return composeReply(true, 'Success', new VehicleCollection($vehicle->refresh()));
    }

    public function deleteImage(Request $request, $id, $imageId)
    {
        $vehicle = Auth::user()->vehicles()->findOrFail($id);
        $vehicleImage = $vehicle->vehicleImages()->findOrFail($imageId);
        $vehicleImage->delete();

        return composeReply(true, 'Success', new VehicleCollection($vehicle->refresh()));
    }
}
