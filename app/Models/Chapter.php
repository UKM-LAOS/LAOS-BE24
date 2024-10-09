<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'lessons' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
