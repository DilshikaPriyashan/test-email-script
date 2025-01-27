<?php

namespace App\Filament\Resources;

use App\Enums\EmailAuthMethods;
use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Filament\Resources\EmailTemplateResource\Pages\CustomizeTemplate;
use App\Models\EmailTemplate;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Email Zone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->icon('heroicon-m-briefcase')
                    ->description('')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) use ($form) {
                                $isCreate = $form->getOperation() === 'create';
                                if ($isCreate) {
                                    $existCount = EmailTemplate::where('api_ref', $state)->where('team_id', Filament::getTenant()->id)->withTrashed()->count();
                                    if ($existCount == 0) {
                                        $set('api_ref', Str::slug($state));
                                    } else {
                                        $existCount++;
                                        $set('api_ref', Str::slug($state.' '.$existCount));
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('api_ref')
                            ->helperText('This key is used to identify the email template when send through the API calls.')
                            ->label('API reference key')
                            ->required()
                            ->maxLength(255)
                            ->disabled($form->getOperation() !== 'create')
                            ->prefix(new HtmlString(Blade::render('<x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="data.name"/>')))
                            ->unique(EmailTemplate::class, 'api_ref', ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                                return $rule->where('team_id', Filament::getTenant());
                            }),
                        Forms\Components\Select::make('auth_mechanism')->options(array_map(function ($value) {
                            return Str::of($value)->replace('_', ' ')
                                ->ucfirst();
                        }, array_column(EmailAuthMethods::cases(), 'value', 'value')))
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Email Template Customization')
                    ->aside()
                    ->description()
                    ->icon('heroicon-m-envelope')
                    ->schema([
                        Repeater::make('attributes')->schema([
                            Forms\Components\TextInput::make('key')
                                ->label('Attribute')
                                ->rules('alpha_dash')
                                ->required()
                                ->maxLength(255)
                                ->distinct()
                                ->extraInputAttributes(['style' => 'font-size:0.7em;padding:0.2em;font-weight:bold;padding-left:0.5em;']),
                            Forms\Components\TextInput::make('default_value')
                                ->label('Default Value')
                                ->extraInputAttributes(['style' => 'font-size:0.7em;padding:0.2em;font-weight:bold;padding-left:0.5em;'])
                                ->maxLength(255),
                        ])->columns(2)
                            ->addActionLabel('Add Attribute'),
                    ]),
                Section::make('Email Settings')
                    ->aside()
                    ->description()
                    ->icon('heroicon-m-cog-6-tooth')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('You can use above added attributed on here. ex: {test} '),
                    ]),

                Section::make('Strict Email Sending Mode')
                    ->aside()
                    ->description()
                    ->icon('heroicon-m-envelope')
                    ->schema([

                        Forms\Components\Radio::make('strict_mode')
                            ->options([
                                'yes' => 'Yes',
                                'no' => 'No',
                            ])
                            ->inline()
                            ->required()
                            ->helperText('Once you check this, you cannot send sender information via the API')
                            ->reactive(),

                        Forms\Components\TagsInput::make('to')
                            ->label('To : ')
                            ->placeholder('Type a mail and press Enter')
                            ->required()
                            ->visible(fn ($get) => $get('strict_mode') === 'yes')
                            ->nestedRecursiveRules([
                                'email',
                                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/',
                            ]),

                        Forms\Components\TagsInput::make('cc')
                            ->label('Cc : ')
                            ->placeholder('Type a mail and press Enter')
                            ->visible(fn ($get) => $get('strict_mode') === 'yes')
                            ->nestedRecursiveRules([
                                'email',
                                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/',
                            ]),

                        Forms\Components\TagsInput::make('bcc')
                            ->label('Bcc : ')
                            ->placeholder('Type a mail and press Enter')
                            ->visible(fn ($get) => $get('strict_mode') === 'yes')
                            ->nestedRecursiveRules([
                                'email',
                                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/',
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('api_ref')
                    ->label('API reference')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('API reference copied')->icon('heroicon-o-clipboard')->iconPosition(IconPosition::After),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Customize Template')
                    ->url(fn (EmailTemplate $record): string => EmailTemplateResource::getUrl('configure', [$record->id]))
                    ->icon('heroicon-o-document-text')->color('success'),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
            'configure' => CustomizeTemplate::route('/{record}/configure'),
        ];
    }
}
