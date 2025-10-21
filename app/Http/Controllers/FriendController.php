<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FriendList;
use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        // This is for the current user's friends page
        $user = Auth::user();
        $isOwnProfile = true;

        // Base query to get the current user's friends
        $query = FriendList::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('friend_id', $user->id);
        });

        // Apply search filter if search parameter exists
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search, $user) {
                // When user_id is the current user, search in friend's nickname
                $q->where(function ($innerQ) use ($search, $user) {
                    $innerQ->where('user_id', $user->id)
                        ->whereHas('friend', function ($sq) use ($search) {
                            $sq->where('nickname', 'like', '%' . $search . '%');
                        });
                })
                    // When friend_id is the current user, search in user's nickname
                    ->orWhere(function ($innerQ) use ($search, $user) {
                        $innerQ->where('friend_id', $user->id)
                            ->whereHas('user', function ($sq) use ($search) {
                                $sq->where('nickname', 'like', '%' . $search . '%');
                            });
                    });
            });
        }

        // Get friends list with friend information
        $friends = $query->with(['user', 'friend'])
            ->paginate(30)->withQueryString();

        // Process friends to display the correct user information
        $processedFriends = $friends->map(function ($friendship) use ($user) {
            // If the current user is in the user_id field, then friend_id is the actual friend
            // Otherwise, user_id is the actual friend
            $friendData = $friendship->user_id == $user->id ? $friendship->friend : $friendship->user;

            return [
                'id' => $friendship->id,
                'friend_id' => $friendData->id,
                'nickname' => $friendData->nickname,
                'profile_picture_url' => $friendData->profile_picture_url,
                'created_at' => $friendship->created_at
            ];
        });

        return view('user.friends.index', [
            'user' => $user,
            'isOwnProfile' => $isOwnProfile,
            'friends' => $processedFriends,
            'paginationLinks' => $friends, // Keep the original collection for pagination
            'activeTab' => 'your-friends',
            'search' => $search
        ]);
    }

    public function show(Request $request, User $user)
    {
        // This is for viewing another user's friends
        $currentUser = Auth::user();
        $isOwnProfile = $currentUser->id === $user->id;

        // If it's the current user, redirect to the index page
        if ($isOwnProfile) {
            return redirect()->route('user.friends.index');
        }

        // Base query for friends of the specified user
        $query = FriendList::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('friend_id', $user->id);
        });

        // Apply search filter if search parameter exists
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search, $user) {
                // When user_id is the specified user, search in friend's nickname
                $q->where(function ($innerQ) use ($search, $user) {
                    $innerQ->where('user_id', $user->id)
                        ->whereHas('friend', function ($sq) use ($search) {
                            $sq->where('nickname', 'like', '%' . $search . '%');
                        });
                })
                    // When friend_id is the specified user, search in user's nickname
                    ->orWhere(function ($innerQ) use ($search, $user) {
                        $innerQ->where('friend_id', $user->id)
                            ->whereHas('user', function ($sq) use ($search) {
                                $sq->where('nickname', 'like', '%' . $search . '%');
                            });
                    });
            });
        }

        // Get friends list of the specified user with friend information
        $friends = $query->with(['user', 'friend'])
            ->paginate(30)->withQueryString();

        // Process friends to display the correct user information
        $processedFriends = $friends->map(function ($friendship) use ($user) {
            // If the specified user is in the user_id field, then friend_id is the actual friend
            // Otherwise, user_id is the actual friend
            $friendData = $friendship->user_id == $user->id ? $friendship->friend : $friendship->user;

            return [
                'id' => $friendship->id,
                'friend_id' => $friendData->id,
                'nickname' => $friendData->nickname,
                'profile_picture_url' => $friendData->profile_picture_url,
                'created_at' => $friendship->created_at
            ];
        });

        return view('user.friends.index', [
            'user' => $user,
            'isOwnProfile' => false,
            'friends' => $processedFriends,
            'paginationLinks' => $friends, // Keep the original collection for pagination
            'activeTab' => 'all-friends'
        ]);
    }

    public function mutual(Request $request, User $user)
    {
        $currentUser = Auth::user();
        $isOwnProfile = $currentUser->id === $user->id;

        // Get the IDs of the current user's friends
        $currentUserFriendIds = FriendList::where(function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id)
                ->orWhere('friend_id', $currentUser->id);
        })->get()->map(function ($friendship) use ($currentUser) {
            return $friendship->user_id == $currentUser->id ? $friendship->friend_id : $friendship->user_id;
        })->toArray();

        // Get the IDs of the viewed user's friends
        $viewedUserFriendIds = FriendList::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('friend_id', $user->id);
        })->get()->map(function ($friendship) use ($user) {
            return $friendship->user_id == $user->id ? $friendship->friend_id : $friendship->user_id;
        })->toArray();

        // Find the intersection of both friend lists (mutual friends)
        $mutualFriendIds = array_intersect($currentUserFriendIds, $viewedUserFriendIds);

        // Get search query if it exists
        $search = $request->input('search');

        // Base query for mutual friends
        $query = User::whereIn('id', $mutualFriendIds);

        // Apply search filter if search parameter exists
        if (!empty($search)) {
            $query->where('nickname', 'like', '%' . $search . '%');
        }

        // Get the actual user objects for the mutual friends with pagination
        $mutualFriendsQuery = $query->paginate(30)->withQueryString();

        // Process the paginated collection
        $processedMutualFriends = $mutualFriendsQuery->map(function ($friend) {
            return [
                'friend_id' => $friend->id,
                'nickname' => $friend->nickname,
                'profile_picture_url' => $friend->profile_picture_url,
                'created_at' => $friend->created_at
            ];
        });

        return view('user.friends.index', [
            'user' => $user,
            'isOwnProfile' => $isOwnProfile,
            'friends' => $processedMutualFriends,
            'paginationLinks' => $mutualFriendsQuery, // Keep the original collection for pagination
            'activeTab' => 'mutual-friends',
            'search' => $search
        ]);
    }

    public function add()
    {
        $user = Auth::user();

        return view('user.friends.index', [
            'user' => $user,
            'isOwnProfile' => true,
            'friends' => collect([]), // Empty collection for friends tab
            'activeTab' => 'add-friend'
        ]);
    }

    public function searchFriends(Request $request)
    {
        $user = Auth::user();
        $searchType = $request->input('search_type');
        $searchValue = $request->input('search_value');

        $results = $this->performFriendSearch($user, $searchValue, $searchType);

        return view('user.friends.index', [
            'user' => $user,
            'isOwnProfile' => true,
            'friends' => collect([]), // Empty collection for friends tab
            'activeTab' => 'add-friend',
            'results' => $results,
            'searchValue' => $searchValue,
            'searchType' => $searchType
        ]);
    }

    /**
     * Helper method to perform friend search
     */
    private function performFriendSearch($user, $searchValue, $searchType)
    {
        if (empty($searchValue)) {
            return collect([]);
        }

        // Get existing friendships
        $friendships = $this->getUserFriendships($user);

        // Get pending friend requests
        $pendingRequests = $this->getUserPendingRequests($user);

        // Search for users based on search type
        $users = $this->searchUsers($user, $searchValue, $searchType);

        // Add friendship status to each user
        return $this->addFriendshipStatus($users, $user, $friendships, $pendingRequests);
    }

    /**
     * Get all friendships for a user
     */
    private function getUserFriendships($user)
    {
        return FriendList::where('user_id', $user->id)
            ->orWhere('friend_id', $user->id)
            ->with(['user', 'friend'])
            ->get();
    }

    /**
     * Get all pending requests for a user
     */
    private function getUserPendingRequests($user)
    {
        return FriendRequest::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->get();
    }

    /**
     * Search for users by code or nickname
     */
    private function searchUsers($user, $searchValue, $searchType)
    {
        $query = User::where('id', '!=', $user->id);

        if ($searchType === 'code') {
            $query->where('unique_code', $searchValue);
        } else {
            $query->where('nickname', 'like', '%' . $searchValue . '%');
        }

        return $query->limit(10)->get();
    }

    /**
     * Add friendship status to each user
     */
    private function addFriendshipStatus($users, $user, $friendships, $pendingRequests)
    {
        $results = collect();

        foreach ($users as $foundUser) {
            // Check friendship status
            $isFriend = $this->isUserFriend($friendships, $user, $foundUser);
            $sentRequest = $this->hasSentRequest($pendingRequests, $user, $foundUser);
            $receivedRequest = $this->hasReceivedRequest($pendingRequests, $user, $foundUser);

            // Set friendship status
            if ($isFriend) {
                $foundUser->friendship_status = 'friend';
            } elseif ($sentRequest) {
                $foundUser->friendship_status = 'sent_request';
            } elseif ($receivedRequest) {
                $foundUser->friendship_status = 'received_request';
            } else {
                $foundUser->friendship_status = 'none';
            }

            $results->push($foundUser);
        }

        return $results;
    }

    /**
     * Check if a user is already a friend
     */
    private function isUserFriend($friendships, $user, $foundUser)
    {
        return $friendships->contains(function ($friendship) use ($user, $foundUser) {
            return ($friendship->user_id == $user->id && $friendship->friend_id == $foundUser->id) ||
                ($friendship->user_id == $foundUser->id && $friendship->friend_id == $user->id);
        });
    }

    /**
     * Check if current user has sent a friend request to the found user
     */
    private function hasSentRequest($pendingRequests, $user, $foundUser)
    {
        return $pendingRequests->contains(function ($request) use ($user, $foundUser) {
            return $request->sender_id == $user->id && $request->receiver_id == $foundUser->id;
        });
    }

    /**
     * Check if current user has received a friend request from the found user
     */
    private function hasReceivedRequest($pendingRequests, $user, $foundUser)
    {
        return $pendingRequests->contains(function ($request) use ($user, $foundUser) {
            return $request->sender_id == $foundUser->id && $request->receiver_id == $user->id;
        });
    }

    public function sendRequest(Request $request)
    {
        $user = Auth::user();
        $receiverId = $request->input('receiver_id');
        $message = '';
        $status = 'success';

        // Validate that the receiver exists and is not the current user
        $receiver = User::find($receiverId);
        if (!$receiver || $receiver->id === $user->id) {
            $message = 'Invalid user selected.';
            $status = 'error';
        }
        // Check if they are already friends
        else {
            $existingFriendship = FriendList::where(function ($query) use ($user, $receiverId) {
                $query->where('user_id', $user->id)->where('friend_id', $receiverId)
                    ->orWhere('user_id', $receiverId)->where('friend_id', $user->id);
            })->first();

            if ($existingFriendship) {
                $message = 'You are already friends with this user.';
                $status = 'error';
            }
            // Check if there's already a pending request
            else {
                $existingRequest = FriendRequest::where(function ($query) use ($user, $receiverId) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $receiverId)
                        ->orWhere('sender_id', $receiverId)->where('receiver_id', $user->id);
                })->first();

                if ($existingRequest) {
                    $message = 'A friend request already exists between you and this user.';
                    $status = 'error';
                }
                // Create the friend request
                else {
                    FriendRequest::create([
                        'sender_id' => $user->id,
                        'receiver_id' => $receiverId,
                    ]);
                    $message = 'Friend request sent successfully!';
                }
            }
        }

        return back()->with($status, $message);
    }

    public function acceptRequest(Request $request)
    {
        $user = Auth::user();
        $senderId = $request->input('sender_id');
        $status = 'success';
        $message = '';

        // Find the friend request
        $friendRequest = FriendRequest::where('sender_id', $senderId)
            ->where('receiver_id', $user->id)
            ->first();

        if ($friendRequest) {
            // Create friendship entry
            FriendList::create([
                'user_id' => $user->id,
                'friend_id' => $senderId
            ]);

            // Delete the request
            $friendRequest->delete();

            $message = 'Friend request accepted!';
        } else {
            $status = 'error';
            $message = 'Friend request not found.';
        }

        return back()->with($status, $message);
    }

    public function declineRequest(Request $request)
    {
        $user = Auth::user();
        $senderId = $request->input('sender_id');
        $status = 'success';
        $message = '';

        // Find the friend request
        $friendRequest = FriendRequest::where('sender_id', $senderId)
            ->where('receiver_id', $user->id)
            ->first();

        if ($friendRequest) {
            // Delete the request
            $friendRequest->delete();
            $message = 'Friend request declined.';
        } else {
            $status = 'error';
            $message = 'Friend request not found.';
        }

        return back()->with($status, $message);
    }

    public function pending()
    {
        $user = Auth::user();

        // Get received friend requests
        $receivedRequests = FriendRequest::where('receiver_id', $user->id)
            ->with('sender')
            ->get();

        // Get sent friend requests
        $sentRequests = FriendRequest::where('sender_id', $user->id)
            ->with('receiver')
            ->get();

        return view('user.friends.index', [
            'user' => $user,
            'isOwnProfile' => true,
            'receivedRequests' => $receivedRequests, // Received requests
            'sentRequests' => $sentRequests,         // Sent requestsinated)
            'activeTab' => 'pending-invites'
        ]);
    }

    public function cancelRequest(User $user)
    {
        $sender = Auth::user();
        $receiverId = $user->id;
        $status = 'success';
        $message = '';

        // Find the friend request
        $friendRequest = FriendRequest::where('sender_id', $sender->id)
            ->where('receiver_id', $receiverId)
            ->first();

        if ($friendRequest) {
            // Delete the request
            $friendRequest->delete();
            $message = 'Friend request canceled.';
        } else {
            $status = 'error';
            $message = 'Friend request not found.';
        }

        return back()->with($status, $message);
    }
}
