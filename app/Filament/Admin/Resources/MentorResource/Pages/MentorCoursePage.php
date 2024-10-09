<?php

namespace App\Filament\Admin\Resources\MentorResource\Pages;

use App\Filament\Admin\Resources\MentorResource;
use App\Models\Course;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MentorCoursePage extends Page implements HasTable
{
    use InteractsWithTable;

    public $mentorId;

    public function mount(): void
    {
        $this->mentorId = request()->route('record');
    }

    protected static string $resource = MentorResource::class;

    protected static string $view = 'filament.admin.resources.mentor-resource.pages.mentor-course-page';

    public function table(Table $table): Table
    {
        return $table
            ->query(Course::query()->whereMentorId($this->mentorId))
            ->columns([
                TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('jumlah_murid')
                    ->label('Jumlah Murid')
                    ->getStateUsing(fn(Model $record) => $record->myCourses->count())
                    ->searchable(),
                TextColumn::make('is_draft')
                    ->label('Status')
                    ->badge()
                    ->color(fn(Model $record) => $record->is_draft ? 'gray' : 'green')
                    ->getStateUsing(fn(Model $record) => $record->is_draft ? 'Draft' : 'Published')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Programming' => 'Programming',
                        'Networking' => 'Networking',
                        'UI/UX' => 'UI/UX',
                        'Cyber Security' => 'Cyber Security',
                        'Digital Marketing' => 'Digital Marketing',
                    ])
                    ->label('Kategori'),
            ], layout: FiltersLayout::AboveContent);
    }
}
