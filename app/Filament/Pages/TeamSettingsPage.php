<?php

namespace App\Filament\Pages;

use App\Models\Team;
use App\Models\TeamSettings;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class TeamSettingsPage extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $view = 'filament.pages.team-settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'System Settings';

    public TeamSettings $teamSettings;

    public Team $team;

    public ?string $smtp_host;

    public ?string $smtp_post;

    public ?string $smtp_encryption;

    public ?int $smtp_port;

    public ?string $smtp_username;

    public ?string $smtp_password;

    public ?string $team_name;

    public ?string $team_slug;

    public ?string $from_name;

    public ?string $from_email;

    protected $rules = [
        'team_name' => 'required',
    ];

    public function mount(): void
    {
        $teamId = Filament::getTenant()->id;
        $this->teamSettings = TeamSettings::firstOrCreate(['team_id' => $teamId], ['team_id' => $teamId]);
        $this->smtp_host = $this->teamSettings->smtp_host;
        $this->smtp_post = $this->teamSettings->smtp_post;
        $this->smtp_encryption = $this->teamSettings->smtp_encryption;
        $this->smtp_port = $this->teamSettings->smtp_port;
        $this->smtp_username = $this->teamSettings->smtp_username;
        $this->smtp_password = $this->teamSettings->smtp_password;
        $this->from_name = $this->teamSettings->from_name;
        $this->from_email = $this->teamSettings->from_email;
        $this->team = Team::findOrFail($teamId);
        $this->team_name = $this->team->name;
        $this->team_slug = $this->team->slug;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Team Settings')->schema(
                [
                    TextInput::make('team_name')
                        ->label('Team Name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('team_slug')
                        ->disabled()
                        ->label('Team Unique Identifier')
                        ->required()
                        ->maxLength(255),
                ]
            )->columns(2)
                ->description("Your's all workspace related information will be listed here.")
                ->icon('heroicon-m-building-office-2')
                ->aside(),

            Section::make('Email Sender Settings')->schema(
                [
                    TextInput::make('smtp_host')
                        ->label('SMTP Host')
                        ->required()
                        ->maxLength(255)->columnSpan(2),
                    TextInput::make('smtp_port')
                        ->label('SMTP Port')
                        ->integer()
                        ->required(),
                    Select::make('smtp_encryption')
                        ->label('SMTP Encryption')
                        ->options([
                            null => 'No Encryption',
                            'ssl' => 'SSL',
                            'tls' => 'TLS',
                        ]),
                    TextInput::make('smtp_username')
                        ->label('SMTP User Name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('smtp_password')
                        ->label('SMTP Password')
                        ->password()
                        ->required()
                        ->maxLength(255),
                ]
            )->columns(2)
                ->description("Your's all email sender's related information will be listed here.")
                ->icon('heroicon-m-building-office-2')
                ->aside(),
            Section::make('Email Sender Settings')->schema(
                [
                    TextInput::make('from_name')
                        ->label('From Name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('from_email')
                        ->label('From Email')
                        ->email()
                        ->required(),
                ]
            )->columns(2)
                ->description("Your's all email sender's related setting will be listed here.")
                ->icon('heroicon-m-building-office-2')
                ->aside(),
        ];
    }

    public function submit()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $this->teamSettings->smtp_host = $this->smtp_host;
            $this->teamSettings->smtp_port = $this->smtp_port;
            $this->teamSettings->smtp_encryption = $this->smtp_encryption;
            $this->teamSettings->smtp_port = $this->smtp_port;
            $this->teamSettings->smtp_username = $this->smtp_username;
            $this->teamSettings->smtp_password = $this->smtp_password;
            $this->teamSettings->from_name = $this->from_name;
            $this->teamSettings->from_email = $this->from_email;
            $this->teamSettings->save();

            $this->team->name = $this->team_name;
            $this->team->save();
            DB::commit();
            Notification::make()
                ->title('Setting has been updated.')
                ->success()
                ->send();
        } catch (\Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->title('Something went wrong')
                ->danger()
                ->send();
        }
    }
}
