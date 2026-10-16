@extends('layouts.admin')

@section('content')
    <section class="panel-head">
        <div>
            <h1>{{ $plan ? 'Edit price' : 'New price' }}</h1>
            <p>{{ $service->title }}</p>
        </div>
        <a class="btn btn-outline" href="{{ route('admin.services.plans.index', $service->id) }}">Service prices</a>
    </section>
    <form class="panel form-panel" method="post" action="{{ $plan ? route('admin.services.plans.update', [$service->id, $plan->id]) : route('admin.services.plans.store', $service->id) }}">
        @csrf
        @if ($plan)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field">
                <span>Certification track<i class="req">*</i></span>
                <span class="field-help">Short badge text, for example PMP, PgMP, PfMP, or RMP.</span>
                <input class="input" type="text" name="label" value="{{ old('label', $plan->label ?? '') }}" maxlength="40" required>
            </label>
            <label class="field">
                <span>Order</span>
                <input class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $plan->sort_order ?? $nextOrder) }}">
            </label>
            <label class="field">
                <span>Price (INR)<i class="req">*</i></span>
                <span class="field-help">The amount the learner pays.</span>
                <input class="input" type="number" name="price" min="0" step="0.01" value="{{ old('price', $plan?->price !== null ? (float) $plan->price : '') }}" required>
            </label>
            <label class="field">
                <span>Original price (INR)</span>
                <span class="field-help">Optional. Shown struck through above the price.</span>
                <input class="input" type="number" name="compare_price" min="0" step="0.01" value="{{ old('compare_price', $plan?->compare_price !== null ? (float) $plan->compare_price : '') }}">
            </label>
            <label class="field span-2">
                <span>Note</span>
                <span class="field-help">Optional internal note about this price.</span>
                <input class="input" type="text" name="description" value="{{ old('description', $plan->description ?? '') }}" maxlength="500">
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $plan->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">Save</button>
            <a class="btn btn-outline" href="{{ route('admin.services.plans.index', $service->id) }}">Cancel</a>
        </div>
    </form>
@endsection
