<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('web.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|size:11',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['phone' => $data['phone'], 'password' => $data['password']])) {
            return back()->withInput()->with('error', '手机号或密码错误');
        }

        if (!Auth::user()->status) {
            Auth::logout();
            return back()->withInput()->with('error', '账号已被禁用');
        }

        return redirect()->intended('/');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function registerForm()
    {
        return view('web.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|string|max:20|unique:users',
            'name' => 'required|string|max:50',
            'phone' => 'required|string|size:11|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
            return back()->withInput()->with('error', '密码需包含大小写字母和数字');
        }

        $user = new User();
        $user->fill($data);
        $user->password = Hash::make($data['password']);
        $user->role = 'student';
        $user->status = 1;
        $user->api_token = Str::random(80);
        $user->save();

        Auth::login($user);

        return redirect('/')->with('success', '注册成功');
    }
}
