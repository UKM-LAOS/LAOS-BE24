<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CourseStackResource\Pages;
use App\Filament\Admin\Resources\CourseStackResource\RelationManagers;
use App\Models\CourseStack;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CourseStackResource extends Resource
{
    protected static ?string $model = CourseStack::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('view_any_course::stack') && Auth::user()->can('view_course::stack');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Stack')
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    SpatieMediaLibraryFileUpload::make('course-stack')
                        ->collection('course-stack')
                        ->rules('max:1024|image|mimes:jpeg,png,jpg')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Stack')
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('course-stack')
                    ->label('Gambar Stack')
                    ->collection('course-stack'),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCourseStacks::route('/'),
            'create' => Pages\CreateCourseStack::route('/create'),
            'edit' => Pages\EditCourseStack::route('/{record}/edit'),
        ];
    }
}
