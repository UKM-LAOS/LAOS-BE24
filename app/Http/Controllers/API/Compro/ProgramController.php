<?php

namespace App\Http\Controllers\API\Compro;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with(['media'])->get(['id', 'program_title', 'activity_title', 'program_slug', 'content']);
        $programs->map(function ($program) {
            $program->thumbnail = $program->getFirstMediaUrl('program-thumbnail');
            unset($program->media);
            return $program;
        });
        return ResponseFormatter::success($programs, 'Data program berhasil diambil');
    }

    public function show($slug)
    {
        $program = Program::with(['media', 'division'])->whereProgramSlug($slug)->first();
        if (!$program) {
            return ResponseFormatter::error('Data program tidak ditemukan', 404);
        }
        $program->thumbnail = $program->getFirstMediaUrl('program-thumbnail');
        $program->documentation = $program->getMedia('program-documentation')->map(function ($media) {
            return $media->getFullUrl();
        });
        unset($program->media);
        return ResponseFormatter::success($program, 'Data program berhasil diambil');
    }
}
