<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::guard('web')->check()) {
            return redirect('/admin');
        } else {
            return view('login');
        }
    }

    public function loginAction(Request $request)
    {
        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::guard('web')->attempt($data)) {
            return redirect('/admin');
        } else {
            session()->flash('error', 'Email atau Password Salah');
            return redirect('login');
        }
    }

    public function logoutAction()
    {
        Auth::guard('web')->logout();
        return redirect('/login');
    }
}
