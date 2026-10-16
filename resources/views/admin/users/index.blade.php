@extends('layouts.admin')

@section('content')
    <section class="panel-head">
        <div>
            <h1>Users</h1>
            <p>Create, manage and review accounts.</p>
        </div>
    </section>
    <section class="panel form-panel user-import">
        <div class="user-import-copy">
            <h2>Bulk import CSV</h2>
            <p>Existing emails are skipped. Imported students get temporary password <code>{{ \App\Services\Users\UserCsvImporter::TEMP_PASSWORD }}</code>.</p>
            <p><a href="{{ route('admin.users.import.template') }}">Download CSV template</a></p>
        </div>
        <form class="user-import-form" method="post" action="{{ route('admin.users.import') }}" enctype="multipart/form-data">
            @csrf
            <label>
                <span class="visually-hidden">CSV file</span>
                <input class="input" type="file" name="csv" accept=".csv,text/csv" required>
            </label>
            @error('csv')<p class="form-error">{{ $message }}</p>@enderror
            <button class="btn btn-primary" type="submit">Upload CSV</button>
        </form>
    </section>
    <section class="panel list-panel">
        <form class="list-tools" method="get">
            <label class="search-shell">
                <span class="visually-hidden">Search</span>
                @include('components.admin.icon', ['name' => 'search'])
                <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name or email">
            </label>
            <select class="select" name="status" aria-label="Status" data-auto-submit>
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
            </select>
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role->label() }}</td>
                            <td><span class="tag {{ $user->status === 'active' ? '' : 'is-bad' }}">{{ ucfirst($user->status) }}</span></td>
                            <td>
                                <div class="row-actions">
                                    @if (! $user->is(auth()->user()))
                                        <form method="post" action="{{ route('admin.users.status', $user) }}">
                                            @csrf
                                            <button class="act {{ $user->status === 'active' ? 'act-danger' : 'act-ok' }}" type="submit">{{ $user->status === 'active' ? 'Block' : 'Unblock' }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No users match this search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.pager', ['paginator' => $users])
    </section>
@endsection
