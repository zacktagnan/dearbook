<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    public $fileDisk = 'public';

    public function index(User $user): Response
    {
        // dd($user);
        return Inertia::render('Profile/Index', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            // 'user' => $user,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateImages(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cover' => 'nullable|image|mimes:png,jpg',
            // 'cover' => 'nullable|image',
            'avatar' => 'nullable|image',
        ]);

        /** @var \App\Models\User $user **/
        // $user = auth()->user();
        // dd('DATA', $data, 'USER', $user);
        // o
        $user = $request->user();

        $cover = $data['cover'] ?? null;
        $avatar = $data['avatar'] ?? null;

        // dd('EXTENSION', $cover->extension());
        $coverFileName = 'cover-' . time() . '.' . $cover->extension();

        if ($cover) {
            $previousCoverImage = $user->cover_path;
            $path = $cover->storeAs('user-' . $user->id, $coverFileName, $this->fileDisk);
            $user->update([
                'cover_path' => $path,
            ]);

            $this->deleteImageFile($previousCoverImage);
        }

        return redirect()->route('profile.index', [
            'user' => $user->username,
        ])->with('status', 'cover-image-updated');
    }

    public function deleteImageFile(string $imageFile)
    {
        if (Storage::disk($this->fileDisk)->exists($imageFile)) {
            Storage::disk($this->fileDisk)->delete($imageFile);
        }
    }
}
