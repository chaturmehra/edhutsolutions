@php
    $pickedCohort = $cohorts->firstWhere('id', $cartLine?->course_schedule_id) ?? $cohorts->first();
    $takenPackage = fn ($tier) => in_array($tier->id, $ownedPackages, true) || isset($pendingPackages[$tier->id]);
    $pickedPackage = $course->packages->firstWhere('id', $cartLine?->course_package_id)
        ?? $course->packages->reject($takenPackage)->sortBy(fn ($tier) => (float) $tier->price)->first();
@endphp
<div class="cohort-modal" data-cohort-modal hidden>
    <button class="cohort-scrim" type="button" data-cohort-close tabindex="-1" aria-label="Close"></button>
    <form class="cohort-dialog" method="post" action="{{ route('cart.store', $course) }}" role="dialog" aria-modal="true" aria-labelledby="cohort-title">
        @csrf
        <header class="cohort-head">
            <div>
                <p class="kicker">Choose a cohort</p>
                <h2 id="cohort-title">Select training date</h2>
            </div>
            <button class="cohort-x" type="button" data-cohort-close aria-label="Close">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>
        </header>
        <div class="cohort-body">
            <fieldset class="cohort-picked">
                <legend>Selected package</legend>
                <div class="cohort-tiers">
                    @foreach ($course->packages as $tier)
                        <label class="cohort-tier">
                            <input type="radio" name="package" value="{{ $tier->id }}" data-tier-price="{{ $tier->money($tier->price) }}" @checked($tier->is($pickedPackage)) @disabled($takenPackage($tier))>
                            <span>
                                <strong>{{ $tier->name }}</strong>
                                <em>{{ in_array($tier->id, $ownedPackages, true) ? 'Purchased' : (isset($pendingPackages[$tier->id]) ? 'Order pending' : $tier->money($tier->price)) }}</em>
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
            @forelse ($cohorts as $cohort)
                @php $cohortDays = $cohort->sessionDays(); @endphp
                <div class="cohort-card {{ $cohort->is($pickedCohort) ? 'is-selected' : '' }}" data-cohort-card>
                    <label class="cohort-pick">
                        <input type="radio" name="schedule" value="{{ $cohort->id }}" @checked($cohort->is($pickedCohort))>
                        <span class="cohort-check" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="cohort-main">
                            <strong class="cohort-range">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ $cohort->rangeLabel() }}
                                @if ($cohort->hasStarted())
                                    <span class="cohort-live">In progress</span>
                                @endif
                            </strong>
                            <span class="cohort-meta">
                                @if ($cohort->time_label)
                                    <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>{{ $cohort->time_label }}</span>
                                @endif
                                @if ($cohort->delivery_mode)
                                    <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 9l9-4.5L21 9l-9 4.5L3 9zM7 11v4.5c0 1.4 2.2 2.5 5 2.5s5-1.1 5-2.5V11" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>{{ $cohort->delivery_mode }}</span>
                                @endif
                                @if ($cohort->hours_label)
                                    <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 4h10M7 20h10M8 4c0 4 8 4 8 8s-8 4-8 8M16 4c0 4-8 4-8 8s8 4 8 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>{{ $cohort->hours_label }}</span>
                                @endif
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8.5" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5 19.5c1-3.2 3.7-5 7-5s6 1.8 7 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Trainer: {{ $cohort->trainerName() }}</span>
                            </span>
                        </span>
                        <span class="cohort-price">
                            <span>Starting at</span>
                            <strong data-cohort-price>{{ $pickedPackage?->money($pickedPackage->price) }}</strong>
                        </span>
                    </label>
                    @if ($cohortDays->isNotEmpty())
                        <button class="cohort-toggle" type="button" data-cohort-dates aria-expanded="false" aria-controls="cohort-days-{{ $cohort->id }}">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <span data-cohort-dates-label>View dates</span>
                            <svg class="cohort-chev" width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <ul class="cohort-days" id="cohort-days-{{ $cohort->id }}" hidden>
                            @foreach ($cohortDays as $cohortDay)
                                <li><strong>{{ $cohortDay->format('d M') }}</strong><span>{{ $cohortDay->format('D') }}</span></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @empty
                <p class="cohort-empty">Dates for the next {{ $course->shortLabel() }} cohort have not been announced yet. Add the package now and the team will confirm your training dates.</p>
            @endforelse
        </div>
        <footer class="cohort-foot">
            <button class="btn btn-ghost" type="button" data-cohort-close>Cancel</button>
            <button class="btn btn-ghost" type="submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 4h2l2.2 10.2a1.5 1.5 0 0 0 1.5 1.2h8.6a1.5 1.5 0 0 0 1.5-1.1L21 8H6.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.5" cy="19.5" r="1.3" fill="currentColor"/><circle cx="17" cy="19.5" r="1.3" fill="currentColor"/></svg>
                Add to Cart
            </button>
            <button class="btn btn-primary" type="submit" name="buy_now" value="1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 3 5 13.5h6L10 21l8-10.5h-6L13 3z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                Buy Now
            </button>
        </footer>
    </form>
</div>
