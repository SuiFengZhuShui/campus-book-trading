<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('sort')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:50']);
        $data['sort'] = Category::max('sort') + 1;

        Category::create($data);

        return back()->with('success', '分类已添加');
    }

    public function update($id, Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:50']);

        $category = Category::findOrFail($id);
        $category->fill($data)->save();

        return redirect('admin/categories')->with('success', '分类已更新');
    }

    public function destroy($id)
    {
        $category = Category::withCount('books')->findOrFail($id);

        if ($category->books_count > 0) {
            return back()->with('error', "该分类下有 {$category->books_count} 本书，无法删除");
        }

        $category->delete();

        return back()->with('success', '已删除');
    }

    public function sort(Request $request)
    {
        $data = $request->validate(['ids' => 'required|array']);

        foreach ($data['ids'] as $i => $id) {
            Category::where('id', $id)->update(['sort' => $i + 1]);
        }

        return $this->success(null, '排序已更新');
    }
}
