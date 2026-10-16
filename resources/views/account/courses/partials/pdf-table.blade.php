@if ($pdfs->isNotEmpty())
    <div class="table-scroll">
        <table class="learn-table learn-list">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pdfs as $pdf)
                    <tr>
                        <td class="learn-num" data-label="#">{{ $loop->iteration }}</td>
                        <td class="learn-name" data-label="Title"><strong>{{ $pdf->title }}</strong></td>
                        <td data-label="Description">{{ $pdf->description ?: '—' }}</td>
                        <td data-label="Action"><a class="learn-view" href="{{ route('learn.pdf', $pdf) }}">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="muted">{{ $empty }}</p>
@endif
