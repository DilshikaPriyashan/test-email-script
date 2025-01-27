<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()->icon('heroicon-o-user-group'),
            'Active' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereHas('teams', function ($query) {
                        $query->whereNotNull('invitation_accepted_at')->where('team_id', Filament::getTenant()->id);
                    });
                })->icon('heroicon-o-user'),
            'Pending' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereHas('teams', function ($query) {
                        $query->whereNull('invitation_accepted_at')->where('team_id', Filament::getTenant()->id);
                    });
                })->icon('heroicon-o-bell-snooze'),
        ];
    }
}
