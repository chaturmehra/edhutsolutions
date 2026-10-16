<section class="section cohort-section {{ $cohortTone ?? '' }}" id="cohorts">
    <div class="wrap">
        <div class="svc-section-head pkg-head">
            <p class="kicker">{{ $cohorts->contains(fn ($cohort) => $cohort->hasStarted()) ? 'Current and upcoming cohorts' : 'Upcoming cohorts' }}</p>
            <h2>{{ $cohorts->contains(fn ($cohort) => $cohort->hasStarted()) ? 'Current and upcoming' : 'Upcoming' }} {{ $course->shortLabel() }} cohorts.</h2>
            <p>{{ $cohorts->isNotEmpty() ? 'Pick the batch that fits your calendar. ' : '' }}Every cohort is live, instructor-led training.</p>
        </div>

        <div class="cohort-list">
            @forelse ($cohorts as $cohortRow)
                @php $cohortRowDays = $cohortRow->sessionDays(); @endphp
                <article class="cohort-row">
                    <div class="cohort-row-grid">
                        <div class="cohort-row-when">
                            <span class="cohort-badge">{{ $course->shortLabel() }}</span>
                            @if ($cohortRow->hasStarted())
                                <span class="cohort-live">In progress</span>
                            @endif
                            <h3>{{ $cohortRow->rangeLabel() }}</h3>
                            @if ($cohortRow->time_label)
                                <p>{{ $cohortRow->time_label }}</p>
                            @endif
                        </div>

                        <ul class="cohort-row-facts">
                            @if ($cohortRow->hours_label)
                                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>{{ $cohortRow->hours_label }}</li>
                            @endif
                            @if ($cohortRow->delivery_mode)
                                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 20h8M12 17v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>{{ $cohortRow->delivery_mode }}</li>
                            @endif
                            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8.5" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5 19.5c1-3.2 3.7-5 7-5s6 1.8 7 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Trainer: {{ $cohortRow->trainerName() }}</li>
                        </ul>

                        <div class="cohort-row-buy">
                            @if ($enrolled)
                                <a class="btn btn-ghost" href="{{ route('learn.show', $course) }}">Continue learning</a>
                            @elseif ($pendingOrder)
                                <a class="btn btn-ghost" href="{{ route('account.orders.show', $pendingOrder) }}">View my order</a>
                            @elseif ($cartLine?->course_schedule_id === $cohortRow->id)
                                <a class="btn btn-primary" href="{{ route('cart.index') }}"><span aria-hidden="true">✓</span> In your cart</a>
                            @else
                                <form method="post" action="{{ route('cart.store', $course) }}">
                                    @csrf
                                    <input type="hidden" name="schedule" value="{{ $cohortRow->id }}">
                                    @if ($entryPackage)
                                        <input type="hidden" name="package" value="{{ $entryPackage->id }}">
                                    @endif
                                    <button class="btn btn-primary" type="submit" @if ($entryPackage) data-cohort-open data-package-id="{{ $entryPackage->id }}" data-schedule-id="{{ $cohortRow->id }}" @endif>
                                        Enroll
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                            @endif
                            <p class="cohort-row-price">
                                @if ($entryPackage)
                                    @if ($entryPackage->compare_price)
                                        <s>{{ $entryPackage->money($entryPackage->compare_price) }}</s>
                                    @endif
                                    <strong>{{ $entryPackage->money($entryPackage->price) }}</strong>
                                @else
                                    <strong>{{ $course->priceLabel() }}</strong>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($cohortRowDays->isNotEmpty())
                        <details class="cohort-plan" @if ($loop->first) open @endif>
                            <summary>
                                <span class="cohort-plan-title">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    Schedule ({{ $cohortRow->scheduleSummary() }})
                                </span>
                                <span class="cohort-plan-toggle">
                                    <span class="cohort-plan-show">Detailed Schedule</span>
                                    <span class="cohort-plan-hide">Hide Schedule</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </summary>
                            <ol class="cohort-sessions">
                                @foreach ($cohortRowDays as $sessionDay)
                                    <li>
                                        <span class="cohort-session-no">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span>
                                            <strong>Session {{ $loop->iteration }}</strong>
                                            <small>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                {{ $sessionDay->format('d M Y (D)') }}
                                            </small>
                                        </span>
                                    </li>
                                @endforeach
                            </ol>
                        </details>
                    @endif
                </article>
            @empty
                <p class="cohort-none">
                    New cohort dates are being announced soon.
                    <a href="{{ route('contact', ['message' => 'Please notify me when the next '.$course->shortLabel().' cohort is announced.']) }}">Drop us a note</a>
                    to be notified.
                </p>
            @endforelse
        </div>
    </div>
</section>
