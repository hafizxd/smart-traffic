<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Transformers\DocumentCollection;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Auth::user()->documents;

        return composeReply(true, 'Success', DocumentCollection::collection($documents));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'documents.0.document_type' => 'required|in:KTP,STNK,SIM',
            'documents.0.image' => 'required|image',
            'documents.*.document_type' => 'required|in:KTP,STNK,SIM',
            'documents.*.image' => 'required|image'
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        $createData = [];

        foreach ($request->documents as $document) {
            $fileName = time() . '_' . $document['image']->getClientOriginalName();
            $document['image']->storeAs('documents', $fileName, 'public');

            $createData[] = [
                'document_type' => $document['document_type'],
                'image' => $fileName
            ];
        }

        $documents = Auth::user()->documents()->createMany($createData);

        return composeReply(true, 'Success', DocumentCollection::collection($documents));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image'
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        $document = Auth::user()->documents()->findOrFail($id);

        $fileName = time() . '_' . $request->image->getClientOriginalName();
        $request->image->storeAs('documents', $fileName, 'public');

        $document->update([
            'image' => $fileName
        ]);

        return composeReply(true, 'Success', new DocumentCollection($document->refresh()));
    }

    public function delete(Request $request, $id)
    {
        $document = Auth::user()->documents()->findOrFail($id);
        $document->delete();

        return composeReply(true, 'Success', new DocumentCollection($document));
    }
}
