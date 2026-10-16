@extends('layouts.admin')

@section('content')
    @php
        $noun = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::singular($config['label']));
    @endphp
    <section class="panel-head">
        <div>
            <h1>{{ $config['label'] }}</h1>
            <p>Create, manage and review {{ \Illuminate\Support\Str::lower($config['label']) }}.</p>
        </div>
        <a class="btn btn-blue" href="{{ route('admin.modules.create', $module) }}">+ New {{ $noun }}</a>
    </section>
    <section class="panel list-panel">
        <form class="list-tools" method="get" action="{{ route('admin.modules.index', $module) }}">
            <label class="search-shell">
                <span class="visually-hidden">Search</span>
                @include('components.admin.icon', ['name' => 'search'])
                <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search">
            </label>
            @if (array_key_exists('status', $config['fields']))
                <select class="select" name="status" aria-label="Status" data-auto-submit>
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            @endif
        </form>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        @foreach ($config['columns'] as $label)
                            <th>{{ $label }}</th>
                        @endforeach
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            @foreach ($config['columns'] as $column => $label)
                                <td>
                                @if ($column === 'status')
                                    <span class="tag {{ $record->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($record->status) }}</span>
                                @elseif ($record->{$column} instanceof \DateTimeInterface)
                                    {{ $record->{$column}->format('M j, Y') }}
                                @elseif (in_array($column, ['logo', 'image', 'thumbnail'], true) && filled($record->{$column}))
                                    <img class="list-thumb" src="{{ asset($record->{$column}) }}" alt="">
                                @else
                                    {{ str_contains($column, '.') ? (data_get($record, $column) ?: '—') : $record->{$column} }}
                                @endif
                                </td>
                            @endforeach
                            <td>
                                <div class="row-actions">
                                    @if ($module === 'courses')
                                        <a class="act" href="{{ route('admin.courses.materials', $record->id) }}">Materials</a>
                                    @endif
                                    @if ($module === 'services')
                                        <a class="act" href="{{ route('admin.services.materials', $record->id) }}">Materials</a>
                                        <a class="act" href="{{ route('admin.services.plans.index', $record->id) }}">Prices</a>
                                    @endif
                                    @if ($module === 'quizzes' && $record->course_id)
                                        <a class="act" href="{{ route('admin.courses.questions', [$record->course_id, $record->id]) }}">Questions</a>
                                    @endif
                                    <a class="act" href="{{ route('admin.modules.edit', [$module, $record->id]) }}">Edit</a>
                                    @if (array_key_exists('status', $config['fields']))
                                        <form method="post" action="{{ route('admin.modules.status', [$module, $record->id]) }}">
                                            @csrf
                                            <button class="act act-ok" type="submit">{{ $record->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
                                        </form>
                                    @endif
                                    <form method="post" action="{{ route('admin.modules.destroy', [$module, $record->id]) }}" data-confirm="Delete this {{ $noun }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="act act-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($config['columns']) + 1 }}">Nothing here yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.pager', ['paginator' => $records])
    </section>
@endsection
