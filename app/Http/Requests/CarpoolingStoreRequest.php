<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Constants\CarpoolingStatus;

class CarpoolingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                Rule::exists('vehicles', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->user()->id);
                }),
            ],
            'capacity' => 'required',
            'phone_number' => 'required',
            'departure_info' => 'required',
            'departure_latitude' => 'required',
            'departure_longitude' => 'required',
            'departure_at' => 'required|date',
            'arrive_info' => 'required',
            'arrive_latitude' => 'required',
            'arrive_longitude' => 'required',
            'arrive_estimation' => 'nullable',
            'distance' => 'nullable',
            'note' => 'nullable'
        ];
    }

    public function data()
    {
        return [
            'vehicle_id' => $this->vehicle_id,
            'capacity' => $this->capacity,
            'phone_number' => $this->phone_number,
            'departure_info' => $this->departure_info,
            'departure_latitude' => $this->departure_latitude,
            'departure_longitude' => $this->departure_longitude,
            'departure_at' => $this->departure_at,
            'arrive_info' => $this->arrive_info,
            'arrive_latitude' => $this->arrive_latitude,
            'arrive_longitude' => $this->arrive_longitude,
            'arrive_estimation' => $this->arrive_estimation ?? null,
            'distance' => $this->distance ?? null,
            'note' => $this->note ?? null,
            'status' => CarpoolingStatus::ADVERTISE
        ];
    }
}
