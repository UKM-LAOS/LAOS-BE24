<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = ['id'];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function courseStacks()
    {
        return $this->belongsToMany(CourseStack::class);
    }

    public function myCourses()
    {
        return $this->hasMany(MyCourse::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course-thumbnail')
            ->singleFile();
        $this->addMediaCollection('course-galleries');
    }
}
