<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ArticleResource\Pages;
use App\Filament\Admin\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('view_article') && Auth::user()->can('view_any_article');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('title')
                        ->label('Judul')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->readOnly(true)
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_unggulan')
                        ->label('Unggulan')
                        ->required(),
                    Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'Informasi' => 'Informasi',
                            'Tutorial' => 'Tutorial',
                            'Mitos & Fakta' => 'Mitos & Fakta',
                            'Tips & Trik' => 'Tips & Trik',
                            'Press Release' => 'Press Release',
                        ])
                        ->required(),
                    Select::make('division_id')
                        ->label('Divisi')
                        ->options(fn() => \App\Models\Division::pluck('name', 'id'))
                        ->required(),
                    RichEditor::make('content')
                        ->label('Konten')
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('thumbnail')
                        ->label('Thumbnail')
                        ->collection('article-thumbnail')
                        ->image()
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('division.name')
                    ->label('Divisi'),
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->collection('article-thumbnail')
                    ->label('Thumbnail'),
                ToggleColumn::make('is_unggulan')
                    ->label('Unggulan'),
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
                    ->label('Kategori')
                    ->options([
                        'Informasi' => 'Informasi',
                        'Tutorial' => 'Tutorial',
                        'Mitos & Fakta' => 'Mitos & Fakta',
                        'Tips & Trik' => 'Tips & Trik',
                        'Press Release' => 'Press Release',
                    ]),
                SelectFilter::make('division_id')
                    ->label('Divisi')
                    ->relationship('division', 'name'),
                Filter::make('is_unggulan')
                    ->label('Unggulan')
                    ->query(fn(Builder $query) => $query->where('is_unggulan', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('mark-as-unggulan')
                        ->label('Tandai sebagai Unggulan')
                        ->action(fn(Collection $records) => $records->each->update(['is_unggulan' => true]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-star'),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
