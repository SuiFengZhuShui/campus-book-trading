<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\College;
use App\Major;
use App\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::with('majors.courses')->orderBy('sort')->get();

        return view('admin.colleges.index', compact('colleges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $data['sort'] = College::max('sort') + 1;
        $college = College::create($data);
        Category::create(['name' => $data['name'], 'sort' => $college->sort]);
        return back()->with('success', '学院已添加');
    }

    public function update($id, Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $college = College::findOrFail($id);
        $oldName = $college->name;
        $college->fill($data)->save();
        if ($data['name'] !== $oldName) {
            Category::where('name', $oldName)->update(['name' => $data['name']]);
        }
        return back()->with('success', '已保存');
    }

    public function destroy($id)
    {
        $college = College::withCount('majors')->findOrFail($id);
        if ($college->majors_count > 0) {
            return back()->with('error', "该学院下有 {$college->majors_count} 个专业，无法删除");
        }
        Category::where('name', $college->name)->delete();
        $college->delete();
        return back()->with('success', '已删除');
    }

    public function storeMajor($collegeId, Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $data['college_id'] = $collegeId;
        $data['sort'] = Major::where('college_id', $collegeId)->max('sort') + 1;
        Major::create($data);
        return back()->with('success', '专业已添加');
    }

    public function updateMajor($id, Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        Major::findOrFail($id)->fill($data)->save();
        return back()->with('success', '已保存');
    }

    public function destroyMajor($id)
    {
        $major = Major::withCount('courses')->findOrFail($id);
        if ($major->courses_count > 0) {
            return back()->with('error', "该专业下有 {$major->courses_count} 门课程，无法删除");
        }
        $major->delete();
        return back()->with('success', '已删除');
    }

    public function storeCourse($majorId, Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $data['major_id'] = $majorId;
        $data['sort'] = Course::where('major_id', $majorId)->max('sort') + 1;
        Course::create($data);
        return back()->with('success', '课程已添加');
    }

    public function updateCourse($id, Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        Course::findOrFail($id)->fill($data)->save();
        return back()->with('success', '已保存');
    }

    public function destroyCourse($id)
    {
        Course::findOrFail($id)->delete();
        return back()->with('success', '已删除');
    }
}
