<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function followUnfollow(Request $request, User $user): RedirectResponse
    {
        $msg = '';
        if ($request->follow) {
            Follower::create([
                'followed_id' => $user->id,
                'follower_id' => auth()->id(),
            ]);
            $msg = 'Ahora, ya sigues a "' . $user->name . '".';
        } else {
            Follower::where('followed_id', $user->id)->where('follower_id', auth()->id())->delete();
            $msg = 'Has dejado de seguir a "' . $user->name . '".';
        }

        return back()->with('success', $msg);
    }
}
