<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetHistController extends Controller
{
    /**
     * Show the asset history print view for a given asset.
     */
    public function printACI($userId)
    {
        $user = \App\Models\User::withTrashed()->find($userId);
        if (!$user) {
            abort(404, 'User not found');
        }
        $this->authorize('view', $user);

        $assets = \App\Models\Asset::where('assigned_to', $user)
            ->whereNotNull('last_checkin')
            ->get();

        $users = [$user];
        $snipeSettings = \App\Models\Setting::getSettings();
        return view('users.printACI', compact('users', 'assets', 'snipeSettings'));
    }


}
