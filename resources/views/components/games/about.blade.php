@props(['game'])

<div class="w-2/3">
    <h2 class="font-semibold text-2xl">About This Game</h2>
    <div class="divider m-0"></div>
    <div class="text-justify">
        {!! nl2br(e($game->full_description)) !!}
    </div>
</div>
