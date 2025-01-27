<?php

namespace App\Filament\Resources;

use App\Filament\Resources\APIKeyResource\Pages;
use App\Models\APIKey;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class APIKeyResource extends Resource
{
    protected static ?string $model = APIKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Email Zone';

    protected static ?string $navigationLabel = 'API Keys';

    protected static ?string $label = 'API Key';

    protected static ?string $slug = 'api-keys';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('key')
                    ->password()
                    ->hidden($form->getOperation() === 'create')
                    ->revealable()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('key_rotated_at')
                    ->label('Last key rotation')
                    ->hidden($form->getOperation() === 'create')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Modified')->icon('heroicon-o-arrow-path')->iconPosition(IconPosition::Before),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('rotate_api_key')
                    ->label('Rotate API key')
                    ->icon('heroicon-o-arrow-path')->action(function ($record) {
                        try {
                            $record->key = $record->generateApiToken();
                            $record->key_rotated_at = Carbon::now();
                            $record->save();

                            Notification::make()
                                ->title('API key has been rotated.')
                                ->success()
                                ->send();
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title('something went wrong')
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalWidth('sm')
                    ->modalAlignment('center')
                    ->modalIcon('heroicon-o-arrow-path')
                    ->modalHeading('Rotate API key')
                    ->modalDescription('Once you rotate API key you need to update it every where that you used the key.else that apis could be able to access the system')
                    ->modalSubmitActionLabel('Yes, Rotate now'),

                Tables\Actions\EditAction::make()->label('View'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListAPIKeys::route('/'),
            'create' => Pages\CreateAPIKey::route('/create'),
            'edit' => Pages\EditAPIKey::route('/{record}/edit'),
        ];
    }
}
