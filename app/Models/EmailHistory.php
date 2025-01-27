<?php

namespace App\Models;

use App\DTOs\EmailActionDTO;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EmailHistory extends Model
{
    use HasFactory;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    protected function casts(): array
    {
        return [
            'history' => 'array',
            'to' => 'array',
            'cc' => 'array',
            'bcc' => 'array',
        ];
    }

    public function lastAction(): EmailActionDTO|null
    {
        $lastAction=Arr::last($this->history);
        if (empty($lastAction)) {
            return null;
        }
        return new EmailActionDTO(
            action_at:Carbon::parse($lastAction['action_at']),
            how_trigger:$lastAction['how_trigger'],
            triggered_by:$lastAction['triggered_by'],
            status:$lastAction['status'],
            fail_reason:$lastAction['fail_reason'],
        );
    }

    public function updateLastAction(EmailActionDTO $emailActionDTO)
    {
        if (empty($this->history)) {
            $this->history = $emailActionDTO->toArray();
            return;
        }

        $actionCount=count($this->history);
        $this->history[$actionCount-1]=$emailActionDTO->toArray();
    }

    public function addAction(EmailActionDTO $action)
    {
        $history = $this->history ?? []; 
        $history[] = $action->toArray();
        $this->history = $history; 
    }

    public function getHistory(): Collection
    {
        $actions=collect([]);
        foreach ($this->history as $key => $action) {
            $actions->add( new EmailActionDTO(
                action_at:Carbon::parse($action['action_at']),
                how_trigger:$action['how_trigger'],
                triggered_by:$action['triggered_by'],
                status:$action['status'],
                fail_reason:$action['fail_reason'],
            ));
        }

        return $actions;
    }

}
