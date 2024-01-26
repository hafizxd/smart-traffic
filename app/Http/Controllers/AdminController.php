<?php

namespace App\Http\Controllers;
use App\Models\Comment;
use App\Models\Document;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function comments()
    {
        $comments = Comment::all(['name', 'email', 'message', 'created_at']);

        return view('admin/pages/comment', compact('comments'));
    }
    public function verifView()
    {
        $documents = Document::with('user')->get();
        return view('admin.pages.user', compact('documents'));
    }
    public function verifyDocument($documentId)
    {
        $document = Document::findOrFail($documentId);

        switch ($document->document_type) {
            case 'KTP':
            case 'STNK':
            case 'SIM':
                $document->update(['is_verified' => true]);
                break;
            default:
                break;
        }

        return redirect()->route('admin.pages.user');
    }
}
