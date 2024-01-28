<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Carpooling;
use App\Constants\CarpoolingStatus;
use App\Constants\CarpoolingPassangerStatus;
use App\Transformers\DocumentCollection;
use App\Transformers\CarpoolingCollection;
use App\Transformers\CarpoolingPassangerCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\PassangerStoreRequest;
use App\Http\Requests\CarpoolingStoreRequest;

class CarpoolingController extends Controller
{
    public function index(Request $request)
    {
        $query = Carpooling::with(['driver', 'vehicle']);

        if (isset($request->is_advertised)) {
            $query->where('status', CarpoolingStatus::ADVERTISE);
        }

        if (isset($request->departure_date)) {
            $query->whereDate('departure_at', $request->departure_date);
        }

        if (isset($request->q)) {
            $query->where(function ($query) use ($request) {
                $query->where('departure_info', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('arrive_info', 'LIKE', '%' . $request->q . '%');
            });
        }

        $carpoolings = $query
            ->where('departure_at', '>=', date('Y-m-d') . ' 00:00:00')
            ->orderBy('departure_at', 'asc')
            ->get();

        return composeReply(true, 'Success', CarpoolingCollection::collection($carpoolings));
    }

    public function indexMine(Request $request)
    {
        $query = Auth::user()->carpoolings()->with(['driver', 'vehicle', 'carpoolingPassangers.passanger']);

        if (isset($request->departure_date)) {
            $query->whereDate('departure_at', $request->departure_date);
        }

        if (isset($request->q)) {
            $query->where(function ($query) use ($request) {
                $query->where('departure_info', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('arrive_info', 'LIKE', '%' . $request->q . '%');
            });
        }

        $carpoolings = $query->orderBy('departure_at', 'desc')->get();

        return composeReply(true, 'Success', CarpoolingCollection::collection($carpoolings));
    }

    public function store(CarpoolingStoreRequest $request)
    {
        // Validate all user documents is verified
        $user = User::whereHas('documents', function ($query) {
            $query->where('document_type', 'KTP')
                ->where('is_verified', true);
        })->whereHas('documents', function ($query) {
            $query->where('document_type', 'STNK')
                ->where('is_verified', true);
        })->whereHas('documents', function ($query) {
            $query->where('document_type', 'SIM')
                ->where('is_verified', true);
        })
            ->where('id', Auth::user()->id)
            ->first();

        if (!isset($user)) {
            return composeReply(false, 'Please complete all the required documents / wait for document verification by admin', [], 400);
        }

        $carpooling = $user->carpoolings()->create($request->data());

        return composeReply(true, 'Success', new CarpoolingCollection($carpooling));
    }

    public function show(Request $request, $id)
    {
        $carpooling = Carpooling::with(['driver', 'vehicle', 'carpoolingPassangers.passanger'])->findOrFail($id);

        return composeReply(true, 'Success', new CarpoolingCollection($carpooling));
    }

    public function update(CarpoolingStoreRequest $request, $id)
    {
        $carpooling = Auth::user()->carpoolings()->findOrFail($id);

        if ($carpooling->carpoolingPassangers()->where('status', CarpoolingPassangerStatus::DEAL)->exists()) {
            return composeReply(false, 'Carpooling tidak bisa di edit karena sudah ada deal dengan penumpang', [], 400);
        }

        $carpooling->update($request->data());

        return composeReply(true, 'Success', new CarpoolingCollection($carpooling));
    }

    public function delete($id)
    {
        $carpooling = Auth::user()->carpoolings()->findOrFail($id);

        if ($carpooling->carpoolingPassangers()->where('status', CarpoolingPassangerStatus::DEAL)->exists()) {
            return composeReply(false, 'Carpooling tidak bisa di edit karena sudah ada deal dengan penumpang', [], 400);
        }

        $carpooling->delete();

        return composeReply(true, 'Success', []);
    }





    // -
    // -
    // ---- PASSANGER -----
    // -
    // -

    public function historyPassanger()
    {
        $passangers = Auth::user()->carpoolingPassangers()
            ->with(['carpooling'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($passangers as $key => $value) {
            $carpooling = new CarpoolingCollection($value->carpooling);
            unset($passangers[$key]->carpooling);

            $passangers[$key]->carpooling_data = $carpooling;
        }

        return composeReply(true, 'Success', CarpoolingPassangerCollection::collection($passangers));
    }

    public function indexPassanger($id)
    {
        $passangers = Auth::user()->carpoolings()->findOrFail($id)
            ->carpoolingPassangers()
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return composeReply(true, 'Success', CarpoolingPassangerCollection::collection($passangers));
    }

    public function showMinePassanger($id)
    {
        $passanger = Carpooling::whereNot('driver_id', Auth::user()->id)->findOrFail($id)
            ->carpoolingPassangers()
            ->where('passanger_id', Auth::user()->id)
            ->first();

        return composeReply(true, 'Success', isset($passanger) ? new CarpoolingPassangerCollection($passanger) : []);
    }

    public function storePassanger(PassangerStoreRequest $request, $id)
    {
        $carpooling = Carpooling::whereNot('driver_id', Auth::user()->id)->findOrFail($id);
        if ($carpooling->status != CarpoolingStatus::ADVERTISE) {
            return composeReply(false, 'Carpooling sudah penuh / berjalan.', [], 400);
        }

        $existsPassangerId = $carpooling->carpoolingPassangers()->where('passanger_id', Auth::user()->id)->exists();
        if ($existsPassangerId) {
            return composeReply(false, 'Anda sudah mengirimkan permintaan pada carpooling ini.', [], 400);
        }

        $currentPassangerCount = $carpooling->carpoolingPassangers()->whereNot('status', CarpoolingPassangerStatus::NEGOTIATE)->sum('passage_count');
        if ($carpooling->capacity < ($currentPassangerCount + $request->passage_count)) {
            return composeReply(false, 'Jumlah kursi penumpang tidak mencukupi', [], 400);
        }

        $passanger = $carpooling->carpoolingPassangers()->create($request->data());

        return composeReply(true, 'Success', new CarpoolingPassangerCollection($passanger));
    }

    public function updatePricePassanger(Request $request, $id, $passangerId)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        $carpooling = Auth::user()->carpoolings()->findOrFail($id);
        $passanger = $carpooling->carpoolingPassangers()->findOrFail($passangerId);

        $currentPassangerCount = $carpooling->carpoolingPassangers()->whereNot('status', CarpoolingPassangerStatus::NEGOTIATE)->sum('passage_count');
        if ($carpooling->capacity < ($currentPassangerCount + $passanger->passage_count)) {
            return composeReply(false, 'Jumlah kursi penumpang tidak mencukupi', [], 400);
        }

        $passanger->update(['price' => $request->price]);

        return composeReply(true, 'Success', new CarpoolingPassangerCollection($passanger->refresh()));
    }

    public function updateStatusPassanger(Request $request, $id, $passangerId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:2,3',
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        $carpooling = Carpooling::findOrFail($id);
        $passanger = $carpooling->carpoolingPassangers()
            ->where('passanger_id', Auth::user()->id)
            ->findOrFail($passangerId);

        $updateData = [
            'status' => $request->status
        ];

        if ($request->status == CarpoolingPassangerStatus::DEAL) {
            $currentPassangerCount = $carpooling->carpoolingPassangers()->whereNot('status', CarpoolingPassangerStatus::NEGOTIATE)->sum('passage_count');
            if ($carpooling->capacity < ($currentPassangerCount + $passanger->passage_count)) {
                return composeReply(false, 'Jumlah kursi penumpang tidak mencukupi', [], 400);
            }

            $updateData['is_approved'] = true;

        } else if ($request->status == CarpoolingPassangerStatus::DONE) {
            // Update carpooling to DONE if all passangers are DONE
            $undoneExists = $carpooling->carpoolingPassangers()->whereNot('passanger_id', Auth::user()->id)->whereNot('status', CarpoolingPassangerStatus::DONE)->exists();
            if (!$undoneExists) {
                $carpooling->update([
                    'status' => CarpoolingStatus::DONE
                ]);
            }
        }

        $passanger->update($updateData);

        return composeReply(true, 'Success', new CarpoolingPassangerCollection($passanger->refresh()));
    }
}
