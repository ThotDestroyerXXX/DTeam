<?php

namespace App\Http\Controllers\User;

use App\Enums\ItemType;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemLibrary;
use App\Models\Country;
use App\Traits\ImageKitUtility;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Models\Item as ItemModel;

class ProfileController extends Controller
{
    use ImageKitUtility;
    /**
     * Show the profile edit page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $section
     * @return \Illuminate\View\View
     */
    public function edit($section = null)
    {
        // allowed sections (must match partial file names / ids)
        $allowed = ['general', 'avatar', 'background', 'change-password'];

        $active = $section && in_array($section, $allowed) ? $section : 'general';

        // Prepare section-specific data only when requested to keep response light
        $sectionData = [];

        // Current authenticated user
        $user = Auth::user();

        if ($active === 'general') {
            // For general section we pass the user model (with relations if needed)
            // eager load country and any other small relations used by the view
            $sectionData['user'] = $user ? $user->loadMissing('country') : null;
            // Load countries list for the country select in the general partial
            $countries = Country::orderBy('name')->get();
            // provide both a compact variable and inside sectionData for flexibility in views
            $sectionData['countries'] = $countries;
            // also set a top-level variable so partials that expect $countries continue to work
            view()->share('countries', $countries);
        } elseif ($active === 'avatar') {
            // For avatar section we load the user's item libraries so the view
            // can display items the user owns (e.g. avatar items)
            if ($user) {
                $sectionData['itemLibraries'] = $user->items()->where('type', ItemType::AVATAR->value)->get();
                $sectionData['user'] = $user;
            } else {
                $sectionData['itemLibraries'] = collect();
                $sectionData['user'] = null;
            }
        } elseif ($active === 'background') {
            // For background section we load available background items
            $sectionData['backgroundItems'] = $user->items()
                ->where('type', ItemType::BACKGROUND->value)
                ->get();
            $sectionData['user'] = $user;
        } elseif ($active === 'change-password') {
            // Change password typically doesn't need extra data beyond the user
            $sectionData['user'] = $user;
        }

        return view('user.profile.edit', [
            'activeSection' => $active,
            'sectionData' => $sectionData,
        ]);
    }

    /**
     * Update user's avatar either by uploaded file or by selecting an item from user's library.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return Redirect::back()->with('auth', 'Unauthorized');
        }

        // validate inputs
        $request->validate([
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'selected_item_id' => ['nullable', 'string'],
        ]);

        $selectedItemId = $request->input('selected_item_id');
        $uploadedFile = $request->file('profile_picture');

        // If a library item was selected, prefer it over uploaded file
        if ($selectedItemId) {
            $itemLib = ItemLibrary::with('item')->where('id', $selectedItemId)->where('user_id', $user->id)->first();
            if (!$itemLib || !$itemLib->item) {
                return Redirect::back()->with('error', 'Invalid item selected.');
            }

            // delete previous uploaded image if any
            if ($user->profile_picture_file_id) {
                try {
                    $this->deleteImage($user->profile_picture_file_id);
                } catch (\Exception $e) {
                    Log::error('Failed to delete previous avatar: ' . $e->getMessage());
                }
            }

            $user->profile_picture_url = $itemLib->item->image_url;
            $user->profile_picture_file_id = null;
            $user->save();

            return Redirect::route('user.profile.edit.section', ['section' => 'avatar'])->with('success', 'avatar-updated');
        }

        // Handle uploaded file via ImageKit
        if ($uploadedFile) {
            try {
                // delete previous uploaded image if exists
                if ($user->profile_picture_file_id) {
                    try {
                        $this->deleteImage($user->profile_picture_file_id);
                    } catch (\Exception $e) {
                        Log::error('Failed to delete previous avatar: ' . $e->getMessage());
                    }
                }

                $response = $this->uploadToImageKit(
                    $uploadedFile,
                    'user-' . $user->id . '-avatar-' . time(),
                    'DTeam/avatars',
                    null,
                    null,
                    false
                );

                if ($response && isset($response->result) && $response->error === null) {
                    $user->profile_picture_url = $response->result->url;
                    $user->profile_picture_file_id = $response->result->fileId;
                    $user->save();

                    return Redirect::route('user.profile.edit.section', ['section' => 'avatar'])->with('success', 'avatar-updated');
                }

                return Redirect::back()->with('profile_picture', 'Failed to upload avatar. Please try again.');
            } catch (\Exception $e) {
                Log::error('Avatar upload failed: ' . $e->getMessage());
                return Redirect::back()->with('profile_picture', 'Failed to upload avatar. Please try again.');
            }
        }

        // Nothing changed
        return Redirect::route('user.profile.edit.section', ['section' => 'avatar'])->with('info', 'No changes submitted.');
    }

    /**
     * Update the user's profile background by selecting an existing background item.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBackground(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return Redirect::back()->with('error', 'Unauthorized');
        }

        $data = $request->validate([
            'selected_background_id' => ['required', 'string'],
        ]);

        $bgId = $data['selected_background_id'];

        // ensure the item exists and is of type 'background'
        $item = Item::where('id', $bgId)->where('type', 'background')->first();
        if (! $item) {
            return Redirect::back()->with('error', 'Invalid background selected.');
        }

        $user->background_url = $item->image_url;
        $user->save();

        return Redirect::back()->with('success', 'background updated');
    }

    // update password
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return Redirect::back()->with('error', 'Unauthorized');
        }

        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = bcrypt($data['new_password']);
        $user->save();

        return Redirect::back()->with('success', 'password updated');
    }
}
