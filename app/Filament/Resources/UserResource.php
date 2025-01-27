<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Jobs\ClientTeamInvitationJob;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'User Management';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email address copied')->icon('heroicon-o-clipboard')->iconPosition(IconPosition::After),
                Tables\Columns\TextColumn::make('teams.invitation_accepted_at')
                    ->label('Invitation Accepted At')
                    ->badge()
                    ->state(function ($record) {
                        $team = $record->team()->whereKey(Filament::getTenant()->id)->first();
                        $invitation_accepted_at = $team->pivot->invitation_accepted_at;

                        return empty($invitation_accepted_at) ? 'Pending' : Carbon::parse($invitation_accepted_at)->since();
                    })->icon(fn (string $state): string => match ($state) {
                        'Pending' => 'heroicon-o-clock',
                        default => 'heroicon-o-check-circle',
                    })->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        default => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Invite to new Users')->icon('heroicon-o-user-plus')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->maxWidth('50%')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->modalWidth('sm')
                    ->modalAlignment('center')
                    ->modalIcon('heroicon-o-envelope')
                    ->modalHeading('Invite to new user')
                    ->modalDescription('Upon submission, the user will receive an email containing an invitation link, allowing them to join and access the team. Please enter the user’s name and email address to proceed.')
                    ->modalSubmitActionLabel('Yes, Invite now')
                    ->action(function (array $data, $record): void {
                        ClientTeamInvitationJob::dispatchSync($data['email'], Filament::getTenant()->id, Auth::id());
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('Re-Send Invitation')->icon('heroicon-o-envelope')->disabled(
                    function ($record) {
                        $team = $record->team()->whereKey(Filament::getTenant()->id)->first();
                        $invitation_accepted_at = $team->pivot->invitation_accepted_at;

                        return empty($invitation_accepted_at) ? false : true;
                    }
                )->action(function ($record) {
                    ClientTeamInvitationJob::dispatchSync($record->email, Filament::getTenant()->id, Auth::id());
                }),
                Tables\Actions\DeleteAction::make()->action(function ($record) {
                    $record->team()->wherePivot('team_id', '=', Filament::getTenant()->id)->detach();
                }),
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
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
