<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['sellingBooks', 'orders'])
            ->when($request->role, function ($q, $v) {
                $q->where('role', $v);
            })
            ->when($request->status !== null, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->keyword, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('name', 'like', "%{$v}%")
                      ->orWhere('student_id', 'like', "%{$v}%")
                      ->orWhere('phone', 'like', "%{$v}%");
                });
            })
            ->orderByRaw("FIELD(role, 'admin', 'student')")
            ->orderBy('created_at', 'desc');

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update($id, Request $request)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:50',
            'student_id' => 'nullable|string|max:50',
            'phone' => 'required|string|max:20|unique:users,phone,' . $id,
            'role' => 'required|in:student,admin',
            'status' => 'required|in:0,1',
            'password' => 'nullable|string|min:6|max:50',
        ]);

        $password = $data['password'];
        unset($data['password']);
        $user->fill($data);
        if (!empty($password)) {
            $user->password = bcrypt($password);
        }
        $user->save();

        return back()->with('page_success', "用户 {$user->name} 已更新");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status ? 0 : 1;
        $user->save();

        $msg = $user->status ? '已启用' : '已禁用';
        return back()->with('success', "{$user->name} {$msg}");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == auth()->id()) {
            return back()->with('error', '不能删除自己');
        }

        $user->delete();
        return back()->with('success', "{$user->name} 已删除");
    }
}
