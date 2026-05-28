<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'account' => 'required|string',
            'password' => 'required|string',
        ]);

        $account = $data['account'];

        if (strpos($account, '@') === false && is_numeric($account)) {
            $credentials = ['phone' => $account, 'password' => $data['password']];
        } else {
            $credentials = ['username' => $account, 'password' => $data['password']];
        }

        if (!Auth::attempt($credentials)) {
            return back()->withInput()->with('error', '账号或密码错误');
        }

        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return back()->withInput()->with('error', '无权限访问');
        }

        if (!Auth::user()->status) {
            Auth::logout();
            return back()->withInput()->with('error', '账号已被禁用');
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
