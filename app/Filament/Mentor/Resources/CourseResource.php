<?php

namespace App\Filament\Mentor\Resources;

use App\Filament\Mentor\Resources\ChapterResource\Pages\ListChapters;
use App\Filament\Mentor\Resources\ChapterResource\Pages\CreateChapter;
use App\Filament\Mentor\Resources\ChapterResource\Pages\EditChapter;
use App\Filament\Mentor\Resources\CourseResource\Pages;
use App\Filament\Mentor\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Hidden::make('mentor_id')
                        ->default(auth()->id()),
                    Forms\Components\TextInput::make('title')
                        ->label('Judul')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->readOnly()
                        ->maxLength(255),
                    Select::make('course-stacks')
                        ->label('Stacks')
                        ->relationship('courseStacks', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('thumbnail')
                        ->label('Thumbnail')
                        ->required()
                        ->image()
                        ->rules('image', 'max:1024', 'mimes:png,jpg,jpeg')
                        ->collection('course-thumbnail'),
                    Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'Programming' => 'Programming',
                            'Networking' => 'Networking',
                            'UI/UX' => 'UI/UX',
                            'Cyber Security' => 'Cyber Security',
                            'Digital Marketing' => 'Digital Marketing',
                            'Multimedia' => 'Multimedia',
                        ])
                        ->required(),
                    Select::make('type')
                        ->label('Tipe')
                        ->options([
                            'Free' => 'Gratis',
                            'Premium' => 'Premium',
                        ])
                        ->required(),
                    Select::make('level')
                        ->label('Level')
                        ->options([
                            'All Level' => 'Semua Level',
                            'Beginner' => 'Pemula',
                            'Intermediate' => 'Menengah',
                            'Advance' => 'Lanjutan',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('price')
                        ->label('Harga')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    RichEditor::make('description')
                        ->label('Deskripsi')
                        ->required(),
                    TextInput::make('drive_resource')
                        ->label('Drive Resource')
                        ->placeholder('https://drive.google.com/...')
                        ->required(),
                    Forms\Components\Toggle::make('is_draft')
                        ->label('Draft')
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('attachments')
                        ->label('Lampiran (opsional)')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->rules('max:5120', 'image', 'mimes:png,jpg,jpeg')
                        ->collection('course-attachments'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Course::query()->whereMentorId(auth()->id()))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe'),
                Tables\Columns\TextColumn::make('level')
                    ->label('Level'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                ToggleColumn::make('is_draft')
                    ->label('Draft'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Programming' => 'Programming',
                        'Networking' => 'Networking',
                        'UI/UX' => 'UI/UX',
                        'Cyber Security' => 'Cyber Security',
                        'Digital Marketing' => 'Digital Marketing',
                        'Multimedia' => 'Multimedia',
                    ])
                    ->label('Kategori'),
                SelectFilter::make('type')
                    ->options([
                        'Free' => 'Gratis',
                        'Premium' => 'Premium',
                    ])
                    ->label('Tipe'),
                SelectFilter::make('level')
                    ->options([
                        'All Level' => 'Semua Level',
                        'Beginner' => 'Pemula',
                        'Intermediate' => 'Menengah',
                        'Advance' => 'Lanjutan',
                    ])
                    ->label('Level'),
                SelectFilter::make('is_draft')
                    ->options([
                        '1' => 'Draft',
                        '0' => 'Published',
                    ])
                    ->label('Status'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('Manage')
                    ->label('Kelola')
                    ->color('warning')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn(Course $course) => ListChapters::getUrl(['record' => $course->id])),
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('publish')
                        ->color(Color::Cyan)
                        ->icon('heroicon-o-eye')
                        ->label('Publish')
                        ->action(fn(Collection $records) => $records->each->update(['is_draft' => false])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
