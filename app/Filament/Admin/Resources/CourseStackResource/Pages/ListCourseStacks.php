<?php

namespace App\Filament\Admin\Resources\CourseStackResource\Pages;

use App\Filament\Admin\Resources\CourseStackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseStacks extends ListRecords
{
    protected static string $resource = CourseStackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
