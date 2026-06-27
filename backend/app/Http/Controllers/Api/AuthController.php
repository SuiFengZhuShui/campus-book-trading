<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Order;
use App\Book;
use App\Want;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|string|max:20|unique:users',
            'name' => 'required|string|max:50',
            'phone' => 'required|string|size:11|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
            return $this->error(422, '密码需包含大小写字母和数字');
        }

        $user = new User();
        $user->fill($data);
        $user->password = Hash::make($data['password']);
        $user->role = 'student';
        $user->status = 1;
        $user->api_token = Str::random(80);
        $user->save();

        return $this->success([
            'token' => $user->api_token,
            'user' => $this->userData($user),
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|size:11',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $data['phone'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->error(401, '手机号或密码错误');
        }

        if (!$user->status) {
            return $this->error(401, '账号已被禁用，请联系管理员');
        }

        $user->api_token = Str::random(80);
        $user->save();

        return $this->success([
            'token' => $user->api_token,
            'user' => $this->userData($user),
        ]);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->api_token = null;
        $user->save();

        return $this->success(null, '已退出');
    }

    public function me()
    {
        $user = auth()->user();
        $data = $this->userData($user);
        $data['stats'] = [
            'order_pending' => $this->orderCount($user, 'pending'),
            'order_paid' => $this->orderCount($user, 'paid'),
            'order_confirmed' => $this->orderCount($user, 'confirmed'),
            'order_done' => $this->orderCount($user, 'picked_up'),
            'my_books_active' => Book::where('seller_id', $user->id)->where('status', 'active')->count(),
            'my_wants_active' => Want::where('user_id', $user->id)->where('status', 'active')->count(),
        ];

        return $this->success($data);
    }

    private function orderCount($user, $status)
    {
        // 仅统计买家侧订单，与 Blade profile 一致。
        // 标签"待付款/已付款/待取书/已完成"是买家视角，不应混入卖家订单。
        return Order::where('buyer_id', $user->id)
            ->where('status', $status)
            ->count();
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'student_id' => $user->student_id,
            'name' => $user->name,
            'phone' => $user->phone,
            'role' => $user->role,
            'avatar' => $user->avatar,
            'status' => $user->status,
        ];
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:50',
            'student_id' => 'required|string|max:20|unique:users,student_id,' . $user->id,
            'phone' => 'required|string|size:11|unique:users,phone,' . $user->id,
        ]);

        $user->fill($data)->save();

        return $this->success($this->userData($user), '保存成功');
    }
}
