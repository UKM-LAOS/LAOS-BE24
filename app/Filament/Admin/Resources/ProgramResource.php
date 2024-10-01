<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProgramResource\Pages;
use App\Filament\Admin\Resources\ProgramResource\RelationManagers;
use App\Models\Division;
use App\Models\Program;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('view_program') && Auth::user()->can('view_any_program');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('program_title')
                        ->required()
                        ->label('Judul Program')
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->unique(ignoreRecord: true)
                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('program_slug', Str::slug($state))),
                    Forms\Components\TextInput::make('program_slug')
                        ->required()
                        ->readOnly()
                        ->label('Slug Program')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('activity_title')
                        ->required()
                        ->maxLength(255),
                    Select::make('division_id')
                        ->required()
                        ->label('Divisi')
                        ->options(Division::all()->pluck('name', 'id')),
                    Map::make('location')
                        ->label('Lokasi')
                        ->defaultLocation(latitude: -8.165516031480806, longitude: 113.71727423131937)
                        ->afterStateUpdated(function (Set $set, ?array $state): void {
                            $set('latitude', $state['lat']);
                            $set('longitude', $state['lng']);
                        })
                        ->liveLocation(true, true, 5000)
                        ->showMarker()
                        ->markerColor("#22c55eff")
                        ->showFullscreenControl()
                        ->showZoomControl()
                        ->draggable()
                        ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                        ->zoom(15)
                        ->detectRetina()
                        ->showMyLocationButton()
                        ->extraTileControl([])
                        ->extraControl([
                            'zoomDelta'           => 1,
                            'zoomSnap'            => 2,
                        ]),
                    Forms\Components\TextInput::make('latitude')
                        ->required()
                        ->readOnly()
                        ->numeric(),
                    Forms\Components\TextInput::make('longitude')
                        ->required()
                        ->readOnly()
                        ->numeric(),
                    Forms\Components\DatePicker::make('open_registration')
                        ->required(),
                    Forms\Components\DatePicker::make('close_registration')
                        ->required(),
                    RichEditor::make('content')
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('thumbnail')
                        ->required()
                        ->collection('program-thumbnail'),
                    SpatieMediaLibraryFileUpload::make('documentation')
                        ->collection('program-documentation')
                        ->multiple()
                        ->label('Dokumentasi (Multiple & Nullable)')
                        ->reorderable(),
                    TextInput::make('embedded_gform')
                        ->label('Google Form')
                        ->placeholder('Masukkan link Google Form, contoh: https://docs.google.com/forms/d/e/1FAIpQLSd'),
                    Repeater::make('program_schedules')
                        ->schema([
                            TextInput::make('schedule_title')
                                ->label('Judul Jadwal')
                                ->required()
                                ->maxLength(255),
                            DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->required(),
                            DatePicker::make('end_date')
                                ->label('Tanggal Selesai')
                                ->required(),
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program_title')
                    ->label('Judul Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('division.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('open_registration')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('close_registration')
                    ->date()
                    ->sortable(),
                SpatieMediaLibraryImageColumn::make('program-thumbnail')
                    ->collection('program-thumbnail')
                    ->label('Thumbnail'),
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
                SelectFilter::make('division_id')
                    ->label('Divisi')
                    ->relationship('division', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
