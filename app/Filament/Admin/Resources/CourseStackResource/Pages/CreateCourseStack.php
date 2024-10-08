<?php

namespace App\Filament\Admin\Resources\CourseStackResource\Pages;

use App\Filament\Admin\Resources\CourseStackResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseStack extends CreateRecord
{
    protected static string $resource = CourseStackResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
