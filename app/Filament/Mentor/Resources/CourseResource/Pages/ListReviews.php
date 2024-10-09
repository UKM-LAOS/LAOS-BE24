<?php

namespace App\Filament\Mentor\Resources\CourseResource\Pages;

use App\Filament\Mentor\Resources\CourseResource;
use App\Models\Chapter;
use App\Models\Review;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ListReviews extends Page implements HasTable
{
    use InteractsWithTable;

    public $courseId;

    public function mount(): void
    {
        $this->courseId = request()->route('record');
    }

    protected static string $resource = CourseResource::class;

    protected static string $view = 'filament.mentor.resources.course-resource.pages.list-reviews';

    public function table(Table $table): Table
    {
        return $table
            ->query(Review::query()->where('course_id', $this->courseId))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pengguna'),
                TextColumn::make('rating')
                    ->label('Rating'),
            ]);
    }
}
