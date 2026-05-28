<?php

namespace App\Http\Controllers\Api;

use App\College;
use App\Major;
use App\Course;
use App\Http\Controllers\Controller;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::orderBy('sort')->get();

        return $this->success($colleges->map(function ($c) {
            return ['id' => $c->id, 'name' => $c->name];
        }));
    }

    public function majors($id)
    {
        $majors = Major::where('college_id', $id)->orderBy('sort')->get();

        return $this->success($majors->map(function ($m) {
            return ['id' => $m->id, 'name' => $m->name];
        }));
    }

    public function courses($id)
    {
        $courses = Course::where('major_id', $id)->orderBy('sort')->get();

        return $this->success($courses->map(function ($c) {
            return ['id' => $c->id, 'name' => $c->name];
        }));
    }
}
