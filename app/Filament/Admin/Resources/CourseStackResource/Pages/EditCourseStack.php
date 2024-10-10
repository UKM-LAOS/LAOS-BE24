<?php

namespace App\Filament\Admin\Resources\CourseStackResource\Pages;

use App\Filament\Admin\Resources\CourseStackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseStack extends EditRecord
{
    protected static string $resource = CourseStackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
