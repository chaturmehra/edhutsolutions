@if ($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="">
@else
    {{ $user->initial() }}
@endif
