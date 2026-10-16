@extends('layouts.user')

@section('title', 'My profile')

@section('content')
    @include('account.partials.top', ['heading' => 'My profile'])

    <form class="learn-card profile-form" method="post" action="{{ route('account.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="profile-photo">
            <span class="user-chip profile-chip">@include('components.public.user-avatar', ['user' => $user])</span>
            <label>
                Profile photo
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif">
                @error('avatar')<small class="is-error">{{ $message }}</small>@enderror
            </label>
        </div>
        <div class="profile-grid">
            <label>
                User code
                <input value="{{ $user->learnerCode() }}" readonly>
                <small>Assigned automatically on registration.</small>
            </label>
            <label>
                Name
                <input name="name" value="{{ old('name', $user->name) }}" required maxlength="120">
                @error('name')<small class="is-error">{{ $message }}</small>@enderror
            </label>
            <label>
                Email
                <input value="{{ $user->email }}" readonly>
            </label>
            <label>
                Phone
                <input name="phone" value="{{ old('phone', $user->phone) }}" maxlength="30">
                @error('phone')<small class="is-error">{{ $message }}</small>@enderror
            </label>
            <label>
                Gender
                <select name="gender">
                    <option value="">Select gender</option>
                    @foreach (\App\Models\User::GENDERS as $value => $label)
                        <option value="{{ $value }}" @selected(old('gender', $user->gender) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('gender')<small class="is-error">{{ $message }}</small>@enderror
            </label>
            <label>
                City
                <input name="city" value="{{ old('city', $user->city) }}" maxlength="120">
                @error('city')<small class="is-error">{{ $message }}</small>@enderror
            </label>
            <label>
                Country
                <select name="country_code">
                    <option value="">Select your country</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->code }}" @selected(old('country_code', $user->country_code) === $country->code)>{{ $country->name }}</option>
                    @endforeach
                </select>
                @error('country_code')<small class="is-error">{{ $message }}</small>@enderror
            </label>
            <label>
                New password (optional)
                <input type="password" name="password" autocomplete="new-password">
                @error('password')<small class="is-error">{{ $message }}</small>@enderror
            </label>
            <label>
                Confirm new password
                <input type="password" name="password_confirmation" autocomplete="new-password">
            </label>
        </div>
        <button class="btn btn-primary" type="submit">Save profile</button>
    </form>
@endsection
