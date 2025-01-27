<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ClientRegistrationController extends Controller
{
    public function index($encryptCode)
    {
        try {
            Auth::forgetUser();
            $code = Crypt::decryptString($encryptCode);
            $user = User::whereHas('team', function ($q) use ($code) {
                $q->where('code', $code);
            })->first();
            if (empty($user)) {
                // todo custom error page : code invalid user not found
                abort(401);
            }
            $team = Team::whereHas('users', function ($q) use ($code, $user) {
                $q->where('code', $code);
                $q->where('user_id', $user->id);
            })->first();

            if (! empty($team->pivot->invitation_accepted_at)) {
                // todo custom error page: already approved another req
                abort(401);
            }

            return view('app.team-invitation.index', [
                'user' => $user,
                'team' => $team,
                'code' => $encryptCode,
            ]);
        } catch (DecryptException $e) {
            // todo custom error page: wrong code
            abort(403);
        } catch (Exception $ex) {
            // todo custom error page : other error
            Log::error('CLIENT REG', [$ex->getMessage(), $ex->getTrace()]);
            abort(500);
        }
    }

    public function accept(Request $request)
    {

        if (! empty($request->get('is_new_user'))) {
            $request->validate([
                'full_name' => 'required',
                'password' => 'required|confirmed',
            ]);
        }

        DB::beginTransaction();
        try {
            $code = Crypt::decryptString($request->get('code'));

            $user = User::whereHas('team', function ($q) use ($code) {
                $q->where('code', $code);
            })->first();

            if (empty($user)) {
                // todo custom error page : code invalid user not found
                abort(401);
            }

            $team = Team::whereHas('users', function ($q) use ($code, $user) {
                $q->where('code', $code);
                $q->where('user_id', $user->id);
            })->first();

            if (! empty($request->get('is_new_user'))) {
                $user->name = $request->get('full_name');
                $user->password = Hash::make($request->get('password'));
            }
            $user->email_verified_at = now();

            $user->team()->updateExistingPivot(
                $team->id, [
                    'code' => null,
                    'invitation_accepted_at' => now(),
                ]

            );
            $user->save();
            DB::commit();
            Auth::loginUsingId($user->id);

            return view('app.team-invitation.wellcome', [
                'user' => $user,
                'team' => $team,
                'redirect' => route('filament.app.pages.dashboard', [$team->slug]),
            ]);
        } catch (DecryptException $e) {
            DB::rollBack();
            // todo custom error page: wrong code
            abort(403);
        } catch (Exception $ex) {
            DB::rollBack();
            // todo custom error page : other error
            Log::error('CLIENT ACCEPT', [$ex->getMessage(), $ex->getTrace()]);
            abort(500);
        }
    }
}
