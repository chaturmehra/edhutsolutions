@extends('layouts.admin')

@section('content')
    @include('admin.courses.partials.head', [
        'heading' => $package ? 'Edit package' : 'New package',
        'back' => route('admin.courses.materials', $course->id),
        'backLabel' => 'Course materials',
    ])
    <form class="panel form-panel" method="post" action="{{ $package ? route('admin.courses.packages.update', [$course->id, $package->id]) : route('admin.courses.packages.store', $course->id) }}">
        @csrf
        @if ($package)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field">
                <span>Package name<i class="req">*</i></span>
                <span class="field-help">For example Silver, Gold, or Platinum.</span>
                <input class="input" type="text" name="name" value="{{ old('name', $package->name ?? '') }}" maxlength="80" required>
            </label>
            <label class="field field-check">
                <span>Most popular</span>
                <span class="field-help">Highlights this card with a "Most Popular" badge.</span>
                <input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $package->is_popular ?? false))>
            </label>
            <label class="field">
                <span>Price ({{ $course->currency ?: 'INR' }})<i class="req">*</i></span>
                <span class="field-help">The amount the learner pays.</span>
                <input class="input" type="number" name="price" min="0" step="0.01" value="{{ old('price', $package?->price !== null ? (float) $package->price : '') }}" required>
            </label>
            <label class="field">
                <span>Original price ({{ $course->currency ?: 'INR' }})</span>
                <span class="field-help">Optional. Shown struck through; the % off is worked out from it.</span>
                <input class="input" type="number" name="compare_price" min="0" step="0.01" value="{{ old('compare_price', $package?->compare_price !== null ? (float) $package->compare_price : '') }}">
            </label>
            <label class="field span-2">
                <span>Lead line</span>
                <span class="field-help">Optional line above the list, for example "Everything listed in Silver package, plus:".</span>
                <input class="input" type="text" name="includes_label" value="{{ old('includes_label', $package->includes_label ?? '') }}" maxlength="255">
            </label>
            <label class="field span-2">
                <span>What's included</span>
                <span class="field-help">One benefit per line. Each line shows with a tick.</span>
                <textarea class="textarea" name="features" rows="10">{{ old('features', $package->features ?? '') }}</textarea>
            </label>
            <label class="field">
                <span>Order</span>
                <input class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $package->sort_order ?? $nextOrder) }}">
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $package->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">Save</button>
            <a class="btn btn-outline" href="{{ route('admin.courses.materials', $course->id) }}">Cancel</a>
        </div>
    </form>
@endsection
