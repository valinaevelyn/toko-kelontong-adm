<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required|min:5'
        ];

        $message = [
            'required' => ':attribute must be field',
            'min' => ':attribute must have minimal :min characters',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()->withErrors($validator);
        }

        if (Auth::attempt($request->only(['email', 'password']))) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->with('error', 'Login Failed!');
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerate();

        return redirect()->route('login');
    }
}
