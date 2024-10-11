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
        $courses = Course::with(['chapters', 'media'])->whereIsDraft(false)->orderByDesc('created_at')->paginate($limit);
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

    public function countByCategory()
    {
        $categories = [
            [
                "image" => asset("svg/code.svg"),
                "name" => "Programming",
                "count" => Course::whereCategory("Programming")->count()
            ],
            [
                "image" => asset("svg/network.svg"),
                "name" => "Networking",
                "count" => Course::whereCategory("Networking")->count()
            ],
            [
                "image" => asset("svg/figma.svg"),
                "name" => "UI/UX",
                "count" => Course::whereCategory("UI/UX")->count()
            ],
            [
                "image" => asset("svg/cyber.svg"),
                "name" => "Cyber Security",
                "count" => Course::whereCategory("Cyber Security")->count()
            ],
            [
                "image" => asset("svg/multimedia.svg"),
                "name" => "Multimedia",
                "count" => Course::whereCategory("Multimedia")->count()
            ],
            [
                "image" => asset("svg/market.svg"),
                "name" => "Digital Marketing",
                "count" => Course::whereCategory("Digital Marketing")->count()
            ]
        ];

        return ResponseFormatter::success($categories, 'Data kategori kursus berhasil diambil');
    }
}
