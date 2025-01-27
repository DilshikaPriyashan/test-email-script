<?php

namespace App\Jobs;

use App\Enums\Roles;
use App\Mail\ClientTeamInvitationEmail;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ClientTeamInvitationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected readonly string $userEmail, protected readonly int $teamId, protected readonly int $invitedByUserId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::where('email', $this->userEmail)->first();

        if (empty($user)) {
            $user = new User;
            $user->name = '';
            $user->email = $this->userEmail;
            $user->save();
            $user->assignRole(Roles::Client->value);
        }

        $team = $user->team->where('id', $this->teamId)->first();
        $teamAlreadyAttach = false;
        if (empty($team)) {
            $team = Team::findOrFail($this->teamId);
        } else {
            $teamAlreadyAttach = true;
            if (! empty($team->pivot->invitation_accepted_at)) {
                return;
            }
        }

        $customCode = Str::random(10).time();

        if ($teamAlreadyAttach) {
            $user->team()->updateExistingPivot(
                $team->id, [
                    'code' => $customCode,
                    'last_invitation_send_at' => now(),
                    'invited_by' => $this->invitedByUserId,
                ]

            );
        } else {
            $user->teams()->attach(
                [
                    $this->teamId => [
                        'code' => $customCode,
                        'last_invitation_send_at' => now(),
                        'invited_by' => $this->invitedByUserId,
                    ],
                ]
            );
        }

        $encryptedCode = Crypt::encryptString($customCode);

        Mail::queue(new ClientTeamInvitationEmail($user, $team, $encryptedCode));
    }
}
