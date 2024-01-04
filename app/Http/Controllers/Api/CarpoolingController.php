<?php

namespace App\Http\Controllers\Api;

use App\Transformers\CarpoolingCollection;
use Illuminate\Http\Request;
use App\Constants\CarpoolingStatus;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Transformers\DocumentCollection;
use App\Http\Requests\CarpoolingStoreRequest;
use Illuminate\Support\Facades\Validator;

class CarpoolingController extends Controller
{
    public function index(Request $request)
    {

    }

    public function store(CarpoolingStoreRequest $request)
    {
        // Validate all user documents is verified
        $user = Auth::user()->whereHas('documents', function ($query) {
            $query->where('document_type', 'KTP')
                ->where('is_verified', true);
        })->whereHas('documents', function ($query) {
            $query->where('document_type', 'STNK')
                ->where('is_verified', true);
        })->whereHas('documents', function ($query) {
            $query->where('document_type', 'SIM')
                ->where('is_verified', true);
        })->first();

        if (!isset($user)) {
            return composeReply(false, 'Please complete all the required documents / wait for document verification by admin', [], 400);
        }

        $carpooling = $user->carpoolings()->create($request->data());

        return composeReply(true, 'Success', new CarpoolingCollection($carpooling));
    }
}
