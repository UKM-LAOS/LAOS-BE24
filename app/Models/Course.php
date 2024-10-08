<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function courseStacks()
    {
        return $this->belongsToMany(CourseStack::class);
    }
}
