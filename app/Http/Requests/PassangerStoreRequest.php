<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Constants\CarpoolingStatus;

class PassangerStoreRequest extends FormRequest
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
            'passage_count' => 'required',
            'price' => 'nullable',
            'phone_number' => 'required',
            'pick_type' => 'in:DATANG,JEMPUT',
            'pick_info' => 'nullable',
            'pick_latitude' => 'nullable',
            'pick_longitude' => 'nullable',
            'drop_info' => 'required',
            'drop_latitude' => 'required',
            'drop_longitude' => 'required'
        ];
    }

    public function data()
    {
        return [
            'code' => generateRandomCode('PSG', 'carpooling_passangers', 'code'),
            'passanger_id' => auth()->user()->id,
            'passage_count' => $this->passage_count,
            'phone_number' => $this->phone_number,
            'pick_type' => $this->pick_type,
            'pick_info' => $this->pick_info,
            'pick_latitude' => $this->pick_latitude,
            'pick_longitude' => $this->pick_longitude,
            'drop_info' => $this->drop_info,
            'drop_latitude' => $this->drop_latitude,
            'drop_longitude' => $this->drop_longitude
        ];
    }
}
