{{-- Change password form will go here --}}
<div class="flex flex-col gap-2">
    <h1 class="text-xl font-bold ">Change Password</h1>
    <form method="POST" action="{{ route('user.profile.update.password') }}" novalidate>
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-4">
            <div class="flex flex-col flex-1">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Current Password</legend>
                    <input type="password" name='current_password' class='input' required
                        placeholder="Current Password" />
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">New Password</legend>
                    <input type="password" name='new_password' class='input' required placeholder="New Password" />
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Confirm New Password</legend>
                    <input type="password" name='new_password_confirmation' class='input' required
                        placeholder="Confirm New Password" />
                </fieldset>
                {{-- check for errors --}}
                @if ($errors->any())
                    <div class="mt-2">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
        </div>
    </form>
</div>
