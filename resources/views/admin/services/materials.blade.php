@extends('layouts.admin')

@section('content')
    <section class="panel-head">
        <div>
            <h1>Service materials</h1>
            <p>{{ $service->title }}</p>
        </div>
        <div class="row-actions">
            <a class="btn btn-outline" href="{{ route('admin.services.plans.index', $service->id) }}">Prices</a>
            <a class="btn btn-outline" href="{{ route('admin.modules.index', 'services') }}">All services</a>
        </div>
    </section>
    <p class="material-note">Learners see the active PDFs and videos under My services after an administrator approves their order for this service. {{ $service->enrollments_count === 1 ? '1 learner has' : $service->enrollments_count.' learners have' }} access now.</p>

    @include('admin.courses.partials.media-tables', ['base' => 'admin.services', 'owner' => $service, 'noun' => 'service'])
@endsection
