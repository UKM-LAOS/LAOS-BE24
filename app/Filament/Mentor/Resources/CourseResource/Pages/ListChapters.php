<?php

namespace App\Filament\Mentor\Resources\CourseResource\Pages;

use App\Filament\Mentor\Resources\CourseResource;
use App\Models\Chapter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;

class ListChapters extends Page implements HasTable, HasForms
{
    public function __construct()
    {
        if (!Auth::user()->hasRole('mentor') || (Auth::user()->can('view_any_course') && Auth::user()->can('view_course'))) {
            abort(403, 'Unauthorized');
        }
    }

    use InteractsWithTable, InteractsWithForms;

    public $courseId;

    public function mount()
    {
        $this->form->fill();
        $this->courseId = request()->route('record');
    }

    protected static string $resource = CourseResource::class;

    protected static string $view = 'filament.mentor.resources.course-resource.pages.list-chapters';

    public function table($table)
    {
        return $table
            ->query(Chapter::query()->where('course_id', $this->courseId))
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Materi'),
            ])
            ->actions([
                EditAction::make()
                    ->modal('edit-chapter')
                    ->model(Chapter::class)
                    ->form([
                        Card::make()->schema([
                            Hidden::make('course_id')
                                ->default($this->courseId),
                            TextInput::make('title')
                                ->label('Judul Materi')
                                ->required()
                                ->maxLength(255),
                            Repeater::make('lessons')
                                ->schema([
                                    TextInput::make('title')
                                        ->label('Judul Materi')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('video_url')
                                        ->label('URL Video')
                                        ->required()
                                        ->maxLength(255),
                                    Toggle::make('is_locked')
                                        ->label('Kunci Materi')
                                        ->default(false),
                                ])
                        ])
                    ]),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('Add Chapter')
                ->modal('create-chapter')
                ->model(Chapter::class)
                ->form([
                    Card::make()->schema([
                        Hidden::make('course_id')
                            ->default($this->courseId),
                        TextInput::make('title')
                            ->label('Judul Materi')
                            ->required()
                            ->maxLength(255),
                        Repeater::make('lessons')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Materi')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('video_url')
                                    ->label('URL Video')
                                    ->required()
                                    ->maxLength(255),
                                Toggle::make('is_locked')
                                    ->label('Kunci Materi')
                                    ->default(false),
                            ])
                    ])
                ])
                ->createAnother(false)
        ];
    }
}
