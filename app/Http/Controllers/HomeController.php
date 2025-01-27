<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('filament.admin.pages.dashboard');
            }
            if (count($user->teams) == 1) {
                return redirect(route('filament.app.pages.dashboard', ['tenant' => $user->team->first()->slug]));
            }

            return view('app.home.index', [
                'teams' => $user->teams,
            ]);
        }

        return redirect('/login');
    }
}
