<?php

namespace App\Http\Controllers;
use App\Models\Comment;

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
}
