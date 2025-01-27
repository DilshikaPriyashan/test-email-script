<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailHistoryResource\Pages;
use App\Filament\Resources\EmailHistoryResource\RelationManagers;
use App\Filament\Resources\EmailTemplateResource\Pages\ViewEmailHistory;
use App\Models\EmailHistory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Artisan;

class EmailHistoryResource extends Resource
{
    protected static ?string $model = EmailHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Email Zone';

    protected static ?string $navigationLabel = 'Email Log';

    protected static ?string $label = 'Email Events';

    protected static ?string $slug = 'email-events';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canUpdate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('emailTemplate.name')
                ->searchable(),
                TextColumn::make('created_at')
                ->label('')
                ->icon('heroicon-o-clock')
                ->datetime()->since(),
                TextColumn::make('Status')
                ->label('Invitation Accepted At')
                ->badge()
                ->icon(fn (string $state): string => match ($state) {
                    'Waiting' => 'heroicon-o-clock',
                    'Processing' => 'heroicon-o-squares-plus',
                    'Send' => 'heroicon-o-check',
                    'Failed' => 'heroicon-o-no-symbol',
                })->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'warning',
                    'Processing' => 'gray',
                    'Send' => 'success',
                    'Failed' => 'danger',
                })->state(function ($record) {
                    return ucfirst($record->status);
                }),
            ])
            ->filters([
            ])
            ->actions([
                Action::make('View')
                ->url(fn (EmailHistory $record): string => EmailHistoryResource::getUrl('view', [$record->id]))
                ->icon('heroicon-o-document-text')->color('success'),
                Action::make('re_send_email')
                ->label('Re-Send Email')
                ->disabled(function ($record) {
                    return $record->status != "failed";
                })
                ->icon('heroicon-o-arrow-path')->action(function ($record) {
                    try {
                        Artisan::call('queue:retry',["id"=>$record->job_id]);
                        $record->status="waiting";
                        $record->save();
                        Notification::make()
                            ->title('Email sending job has been dispatched.')
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
                ->modalHeading('Are you sure re-try this job')
                ->modalDescription('Once you press yes, you can not undo this action')
                ->modalSubmitActionLabel('Yes, send now'),
            ])
            ->bulkActions([
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
            'index' => Pages\ListEmailHistories::route('/'),
            'view' => ViewEmailHistory::route('/{record}/view'),
        ];
    }
}
