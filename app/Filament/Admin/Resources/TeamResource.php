<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeamResource\Pages;
use App\Models\Team;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(
                    [
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) use ($form) {
                                $isCreate = $form->getOperation() === 'create';
                                if ($isCreate) {
                                    $existCount = Team::where('slug', $state)->withTrashed()->count();
                                    if ($existCount == 0) {
                                        $set('slug', Str::slug($state));
                                    } else {
                                        $existCount++;
                                        $set('slug', Str::slug($state.' '.$existCount));
                                    }
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled($form->getOperation() !== 'create')
                            ->prefix(new HtmlString(Blade::render('<x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="data.name"/>')))
                            ->unique(Team::class, 'slug', ignoreRecord: true),
                    ]
                )->label('Team Info')->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Team')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Team Identifier')
                    ->color('gray')
                    ->searchable(),
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
                Tables\Actions\DeleteAction::make(),
                Action::make('Access to the Team')
                    ->icon('heroicon-o-briefcase')
                    ->color('secondary')
                    ->url(fn (Team $record): string => route('filament.app.pages.dashboard', $record->slug))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
