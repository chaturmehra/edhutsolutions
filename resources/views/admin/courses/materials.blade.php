@extends('layouts.admin')

@section('content')
    @include('admin.courses.partials.head', [
        'heading' => 'Course materials',
        'back' => route('admin.modules.index', 'courses'),
        'backLabel' => 'All courses',
    ])
    <p class="material-note">Learners see the active PDFs, videos, and quizzes here after an administrator approves their enrollment.</p>

    <section class="material-block">
        <div class="panel-head">
            <div>
                <h2>Mock and practice tests</h2>
                <p>Create the test, then add questions one at a time or upload them in bulk from a CSV file. Mock tests draw a fresh random set on every start.</p>
            </div>
            <a class="btn btn-blue" href="{{ route('admin.courses.quizzes.create', $course->id) }}">+ New quiz</a>
        </div>
        <div class="panel list-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Questions</th>
                            <th>Pass mark</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($course->quizzes as $quiz)
                            <tr>
                                <td>{{ $quiz->title }}</td>
                                <td>{{ $quiz->kindLabel() }}</td>
                                <td>{{ $quiz->questions_count }}{{ $quiz->isMock() && $quiz->questions_per_attempt ? ' ('.$quiz->questions_per_attempt.' per attempt)' : '' }}</td>
                                <td>{{ rtrim(rtrim(number_format((float) $quiz->pass_percentage, 2), '0'), '.') }}%</td>
                                <td><span class="tag {{ $quiz->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($quiz->status) }}</span></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="act" href="{{ route('admin.courses.questions', [$course->id, $quiz->id]) }}">Questions</a>
                                        <a class="act" href="{{ route('admin.courses.quizzes.edit', [$course->id, $quiz->id]) }}">Edit</a>
                                        <form method="post" action="{{ route('admin.courses.quizzes.destroy', [$course->id, $quiz->id]) }}" data-confirm="Delete this quiz?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="act act-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No test has been added for this course yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @include('admin.courses.partials.media-tables', ['base' => 'admin.courses', 'owner' => $course, 'noun' => 'course'])

    <section class="material-block">
        <div class="panel-head">
            <div>
                <h2>Packages</h2>
                <p>Active packages show as pricing cards on the course page. With no package, the course price is used.</p>
            </div>
            <a class="btn btn-blue" href="{{ route('admin.courses.packages.create', $course->id) }}">+ New package</a>
        </div>
        <div class="panel list-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Price</th>
                            <th>Benefits</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($course->packages as $package)
                            <tr>
                                <td>{{ $package->name }} @if ($package->is_popular)<span class="tag">Most popular</span>@endif</td>
                                <td>
                                    {{ $package->money($package->price) }}
                                    @if ($package->discountPercent() > 0)
                                        <span class="meta"><s>{{ $package->money($package->compare_price) }}</s> · {{ $package->discountPercent() }}% off</span>
                                    @endif
                                </td>
                                <td>{{ count($package->featureList()) }}</td>
                                <td>{{ $package->sort_order }}</td>
                                <td><span class="tag {{ $package->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($package->status) }}</span></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="act" href="{{ route('admin.courses.packages.edit', [$course->id, $package->id]) }}">Edit</a>
                                        <form method="post" action="{{ route('admin.courses.packages.destroy', [$course->id, $package->id]) }}" data-confirm="Delete this package?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="act act-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No package has been added for this course yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="material-block">
        <div class="panel-head">
            <div>
                <h2>Cohorts</h2>
                <p>Active cohorts appear on the course page and in the "Select training date" popup until their end date (or their start date when no end date is set). A cohort that has started shows as "In progress".</p>
            </div>
            <a class="btn btn-blue" href="{{ route('admin.courses.cohorts.create', $course->id) }}">+ New cohort</a>
        </div>
        <div class="panel list-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Dates</th>
                            <th>Time</th>
                            <th>Mode</th>
                            <th>Trainer</th>
                            <th>Sessions</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($course->schedules as $cohort)
                            <tr>
                                <td>
                                    {{ $cohort->rangeLabel() }}
                                    @if ($cohort->starts_on && $cohort->starts_on->isPast() && ! $cohort->starts_on->isToday())
                                        <span class="meta">Started</span>
                                    @endif
                                </td>
                                <td>{{ $cohort->time_label ?: '—' }}</td>
                                <td>{{ $cohort->delivery_mode ?: '—' }}</td>
                                <td>{{ $cohort->trainerName() }}</td>
                                <td>{{ $cohort->hours_label ?: '—' }} <span class="meta">{{ $cohort->sessionDays()->count() }} dates</span></td>
                                <td><span class="tag {{ $cohort->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($cohort->status) }}</span></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="act" href="{{ route('admin.courses.cohorts.edit', [$course->id, $cohort->id]) }}">Edit</a>
                                        <form method="post" action="{{ route('admin.courses.cohorts.destroy', [$course->id, $cohort->id]) }}" data-confirm="Delete this cohort?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="act act-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">No cohort has been added for this course yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="material-block">
        <div class="panel-head">
            <div>
                <h2>Course details: Agenda</h2>
                <p>Each active module shows as a card in the Agenda tab on the course page, and as the curriculum in the learner area. Overview and learning objectives are edited on the <a href="{{ route('admin.modules.edit', ['courses', $course->id]) }}">course form</a>.</p>
            </div>
            <a class="btn btn-blue" href="{{ route('admin.courses.modules.create', $course->id) }}">+ New module</a>
        </div>
        <div class="panel list-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Topics</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($course->sections as $module)
                            <tr>
                                <td>{{ $module->title }}</td>
                                <td>{{ $module->lessons_count }}</td>
                                <td>{{ $module->sort_order }}</td>
                                <td><span class="tag {{ $module->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($module->status) }}</span></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="act" href="{{ route('admin.courses.modules.edit', [$course->id, $module->id]) }}">Edit</a>
                                        <form method="post" action="{{ route('admin.courses.modules.destroy', [$course->id, $module->id]) }}" data-confirm="Delete this agenda module?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="act act-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No agenda module has been added for this course yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="material-block">
        <div class="panel-head">
            <div>
                <h2>Course details: Resources</h2>
                <p>Public resources show in the Resources tab. Attach a PDF to make the title open the file for any visitor.</p>
            </div>
            <a class="btn btn-blue" href="{{ route('admin.courses.resources.create', $course->id) }}">+ New resource</a>
        </div>
        <div class="panel list-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>PDF</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($course->resources as $resource)
                            <tr>
                                <td>{{ $resource->title }}</td>
                                <td>
                                    @if ($resource->hasFile())
                                        {{ $resource->original_name ?: 'PDF' }} <span class="meta">{{ \Illuminate\Support\Number::fileSize((int) $resource->file_size) }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $resource->sort_order }}</td>
                                <td><span class="tag {{ $resource->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($resource->status) }}</span></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="act" href="{{ route('admin.courses.resources.edit', [$course->id, $resource->id]) }}">Edit</a>
                                        <form method="post" action="{{ route('admin.courses.resources.destroy', [$course->id, $resource->id]) }}" data-confirm="Delete this resource?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="act act-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No resource has been added for this course yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
