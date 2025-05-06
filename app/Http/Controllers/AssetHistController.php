<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetHistController extends Controller
{
    /**
     * Show the asset history print view for a given asset.
     */
    public function printHist($userId)
    {
        $user = \App\Models\User::withTrashed()->find($userId);
        if (!$user) {
            abort(404, 'User not found');
        }
        $this->authorize('view', $user);
        // Ambil hanya asset dengan status checkin
        $assets = $user->assets()->whereHas('assets', function($q) {
            $q->where('name', 'checkin');
        })->get();
        $users = [$user];
        $snipeSettings = \App\Models\Setting::getSettings();
        return view('users.printHist', compact('users', 'assets', 'snipeSettings'));
    }
}
