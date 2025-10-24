<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PublisherProfileUpdateRequest;
use App\Models\Country;
use App\Models\FriendList;
use App\Models\GameLibrary;
use App\Models\User;
use App\Traits\ImageKitUtility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use ImageKitUtility;


    public function index(User $user): View
    {

        $query = FriendList::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('friend_id', $user->id);
        });

        return view('user.profile.index', [
            'user' => $user,
            'userGames' => GameLibrary::where('user_id', $user->id)->with('game')->limit(3)->orderBy('created_at', 'desc')->get(),
            'userFriends' => $query->with('friend')->limit(5)->get()
        ]);
    }


    /**
     * Display the user's profile form.
     */
    public function editUser(Request $request): View
    {
        return view('user.profile.edit', [
            'user' => $request->user(),
            'countries' => Country::all(),
        ]);
    }

    public function editPublisher(Request $request): View
    {
        return view('publisher.profile.edit', [
            'publisher' => $request->user()->publisher,
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

        return Redirect::route('store.index')->with('status', 'profile-updated');
    }

    public function updatePublisher(PublisherProfileUpdateRequest $request): RedirectResponse
    {
        $inputs = $request->validated();

        $publisher = $request->user()->publisher;
        $publisher->name = $inputs['name'];
        $publisher->website = $inputs['website'];

        // Handle image removal if remove_image is checked
        if (isset($inputs['remove_image']) && $inputs['remove_image'] && $publisher->image_file_id) {
            // Import the ImageKit utility trait if it's not already included
            $this->deleteImage($publisher->image_file_id);
            $publisher->image_url = null;
            $publisher->image_file_id = null;
        }

        // Handle new image upload
        if (isset($inputs['image'])) {
            // If there's an existing image, delete it first (if not already deleted by remove_image)
            if ($publisher->image_file_id && !isset($inputs['remove_image'])) {
                $this->deleteImage($publisher->image_file_id);
            }

            // Upload new image to ImageKit
            $image = $inputs['image'];
            $response = $this->uploadToImageKit(
                $image,
                'publisher-' . $publisher->id . '-' . time(),
                'DTeam/publishers',
                null,
                null,
                false
            );

            if ($response && $response->error === null && isset($response->result)) {
                $publisher->image_url = $response->result->url;
                $publisher->image_file_id = $response->result->fileId;
            } else {
                return Redirect::back()->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        }

        $publisher->save();

        return Redirect::route('publisher.games.index')->with('success', 'Profile updated successfully.');
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
