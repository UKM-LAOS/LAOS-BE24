<?php

namespace App\Http\Controllers\Api\Course;

use App\Http\Controllers\API\Compro\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 6);
        $courses = Course::with('chapters')->paginate($limit);
        return ResponseFormatter::success($courses, 'Data kursus berhasil diambil');
    }

    public function search(Request $request)
    {
        $category = $request->input('category');
        $level = $request->input('level');
        $type = $request->input('type');

        $course = Course::query();

        if ($category) {
            $course->whereCategoryId($category);
        }

        if ($level) {
            $course->whereLevel($level);
        }

        if ($type) {
            $course->whereType($type);
        }

        return ResponseFormatter::success($course->paginate(6), 'Data kursus berhasil diambil');
    }

    public function show($slug)
    {
        $course = Course::with(['mentor', 'courseStacks', 'chapters', 'reviews', 'reviews.user'])->where('slug', $slug)->first();

        if (!$course) {
            return ResponseFormatter::error('Data kursus tidak ada', 404);
        }

        return ResponseFormatter::success($course, 'Data kursus berhasil diambil');
    }
}
