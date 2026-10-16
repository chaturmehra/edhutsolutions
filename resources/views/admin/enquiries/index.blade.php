@extends('layouts.admin')

@section('content')
    <section class="panel-head">
        <div>
            <h1>Enquiries</h1>
            <p>Create, manage and review messages from the site.</p>
        </div>
    </section>
    <section class="panel list-panel">
        <form class="list-tools" method="get">
            <label class="search-shell">
                <span class="visually-hidden">Search</span>
                @include('components.admin.icon', ['name' => 'search'])
                <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name, email, or subject">
            </label>
            <select class="select" name="status" aria-label="Status" data-auto-submit>
                <option value="">All statuses</option>
                <option value="new" @selected(request('status') === 'new')>New</option>
                <option value="read" @selected(request('status') === 'read')>Read</option>
            </select>
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($enquiries as $enquiry)
                        <tr data-enquiry-row="{{ $enquiry->id }}">
                            <td>{{ $enquiry->name }}<br><span class="meta">{{ $enquiry->email }}</span></td>
                            <td>{{ $enquiry->subject }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($enquiry->message, 90) }}</td>
                            <td><span class="tag {{ $enquiry->status === 'new' ? 'is-wait' : '' }}" data-enquiry-status>{{ ucfirst($enquiry->status) }}</span></td>
                            <td>
                                <div class="row-actions">
                                    <button class="act act-view" type="button" data-enquiry-open="enquiry-{{ $enquiry->id }}" data-read-url="{{ route('admin.enquiries.read', $enquiry) }}" data-status="{{ $enquiry->status }}">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.8" stroke="currentColor" stroke-width="1.8"/></svg>
                                        View
                                    </button>
                                    <form method="post" action="{{ route('admin.enquiries.update', $enquiry) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="act act-ok" type="submit" data-enquiry-toggle>{{ $enquiry->status === 'new' ? 'Mark read' : 'Mark new' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No enquiries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.pager', ['paginator' => $enquiries])
    </section>

    @foreach ($enquiries as $enquiry)
        <dialog class="enquiry-dialog" id="enquiry-{{ $enquiry->id }}" aria-labelledby="enquiry-{{ $enquiry->id }}-title">
            <div class="enquiry-dialog-head">
                <div>
                    <p class="meta">Received {{ $enquiry->created_at?->format('d M Y, h:i A') }}</p>
                    <h2 id="enquiry-{{ $enquiry->id }}-title">{{ $enquiry->subject }}</h2>
                </div>
                <button class="enquiry-close" type="button" data-dialog-close aria-label="Close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <dl class="enquiry-facts">
                <div><dt>Name</dt><dd>{{ $enquiry->name }}</dd></div>
                <div><dt>Email</dt><dd><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></dd></div>
                <div><dt>Phone</dt><dd>@if (filled($enquiry->phone))<a href="tel:{{ $enquiry->phone }}">{{ $enquiry->phone }}</a>@else - @endif</dd></div>
                <div><dt>Status</dt><dd><span class="tag {{ $enquiry->status === 'new' ? 'is-wait' : '' }}" data-enquiry-status>{{ ucfirst($enquiry->status) }}</span></dd></div>
            </dl>
            <h3>Message</h3>
            <p class="enquiry-message">{{ $enquiry->message }}</p>
            <div class="enquiry-dialog-foot">
                <button class="btn btn-outline" type="button" data-dialog-close>Close</button>
            </div>
        </dialog>
    @endforeach
@endsection
