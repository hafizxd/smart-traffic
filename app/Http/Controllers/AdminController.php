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
        $documents = Document::findOrFail($documentId);

        switch ($documents->document_type) {
            case 'KTP':
            case 'STNK':
            case 'SIM':
                $documents->update(['is_verified' => true]);
                break;
            default:
                break;
        }

        return redirect()->route('admin.pages.user');
    }
}
