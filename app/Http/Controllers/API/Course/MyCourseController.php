<?php

namespace App\Http\Controllers\API\Course;

use App\Http\Controllers\API\Compro\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\MyCourse;
use Illuminate\Http\Request;

class MyCourseController extends Controller
{
    public function index()
    {
        $myCourses = MyCourse::with('transactions')->whereHas('transaction', function ($query) {
            $query->whereStatus('Success');
        })->whereUserId(auth('api')->user()->id)->paginate(12);

        return ResponseFormatter::success($myCourses, 'Data My Course berhasil diambil');
    }

    public function show($slug)
    {
        $myCourse = MyCourse::with('course', 'course.chapters', 'courseStacks')
            ->whereHas('user', function ($query) {
                $query->whereId(auth('api')->user()->id);
            })
            ->whereHas('course', function ($query) use ($slug) {
                $query->whereSlug($slug);
            })
            ->first();
        if (!$myCourse) {
            return ResponseFormatter::error('Data My Course tidak ada', 404);
        }

        return ResponseFormatter::success($myCourse, 'Data My Course berhasil diambil');
    }
}
