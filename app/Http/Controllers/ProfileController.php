<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        if ($request->file('profile_photo')) {
            $photo = $request->file('profile_photo');
            if (! $photo->isValid()) {
                $errorCode = $photo->getError();
                $errorMessage = match ($errorCode) {
                    UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The profile photo is too large. Please choose a smaller image.',
                    UPLOAD_ERR_PARTIAL => 'The profile photo was only partially uploaded. Please try again.',
                    UPLOAD_ERR_NO_FILE => 'No profile photo was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Server error: missing temporary folder. Contact support.',
                    UPLOAD_ERR_CANT_WRITE => 'Server error: unable to write uploaded file.',
                    UPLOAD_ERR_EXTENSION => 'Server error: upload stopped by extension.',
                    default => 'The profile photo failed to upload. Please try a different image.',
                };

                Log::error('Profile photo upload failed', [
                    'user_id' => $request->user()->id,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                ]);

                return Redirect::back()->withErrors(['profile_photo' => $errorMessage])->withInput();
            }

            $validated['profile_photo_path'] = $photo->store('profile-photos', 'public');
            Log::info('Profile photo uploaded', [
                'user_id' => $request->user()->id,
                'path' => $validated['profile_photo_path'],
            ]);
        }

        $request->user()->fill(Arr::except($validated, ['profile_photo']));

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($request->expectsJson()) {
            $user = $request->user()->fresh();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_path
                        ? Storage::url($user->profile_photo_path)
                        : null,
                ],
            ]);
        }

        return Redirect::route('chat.index')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
