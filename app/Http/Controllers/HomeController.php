<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class HomeController extends Controller
{
    public function index()
    {
        $comments = Comment::latest()->paginate(5);

        return view('index', compact('comments'));
    }
}
