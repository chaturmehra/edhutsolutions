{{-- Expects $base ('admin.courses' or 'admin.services'), $owner, and $noun ('course' or 'service'). --}}
<section class="material-block">
    <div class="panel-head">
        <div>
            <h2>PDFs</h2>
            <p>Upload a study PDF. Files stay private and open only for an approved learner.</p>
        </div>
        <a class="btn btn-blue" href="{{ route($base.'.pdfs.create', $owner->id) }}">+ Upload PDF</a>
    </div>
    <div class="panel list-panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>File</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($owner->pdfs as $pdf)
                        <tr>
                            <td>{{ $pdf->title }}@if ($pdf->description)<span class="meta cell-note">{{ \Illuminate\Support\Str::limit($pdf->description, 80) }}</span>@endif</td>
                            <td>{{ $pdf->original_name ?: 'PDF' }} <span class="meta">{{ \Illuminate\Support\Number::fileSize((int) $pdf->file_size) }}</span></td>
                            <td>{{ $pdf->sort_order }}</td>
                            <td><span class="tag {{ $pdf->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($pdf->status) }}</span></td>
                            <td>
                                <div class="row-actions">
                                    <a class="act" href="{{ route($base.'.pdfs.edit', [$owner->id, $pdf->id]) }}">Edit</a>
                                    <form method="post" action="{{ route($base.'.pdfs.destroy', [$owner->id, $pdf->id]) }}" data-confirm="Delete this PDF?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="act act-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No PDF has been added for this {{ $noun }} yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="material-block">
    <div class="panel-head">
        <div>
            <h2>Videos</h2>
            <p>Upload an MP4 or save a YouTube, Vimeo, Google Drive or Loom link. Learners play it in a popup on the {{ $noun === 'service' ? 'My services' : 'course' }} page.</p>
        </div>
        <a class="btn btn-blue" href="{{ route($base.'.videos.create', $owner->id) }}">+ Add video</a>
    </div>
    <div class="panel list-panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Source</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($owner->videos as $video)
                        <tr>
                            <td>{{ $video->title }}</td>
                            <td>
                                {{ $video->source_type === 'upload' ? 'Uploaded MP4' : 'Link' }}
                                @if ($video->source_type === 'link')
                                    <span class="meta cell-note">{{ $video->player() ? 'Plays inside the '.$noun : 'Opens in a new tab' }}</span>
                                @endif
                            </td>
                            <td>{{ $video->sort_order }}</td>
                            <td><span class="tag {{ $video->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($video->status) }}</span></td>
                            <td>
                                <div class="row-actions">
                                    <a class="act" href="{{ route($base.'.videos.edit', [$owner->id, $video->id]) }}">Edit</a>
                                    <form method="post" action="{{ route($base.'.videos.destroy', [$owner->id, $video->id]) }}" data-confirm="Delete this video?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="act act-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No video has been added for this {{ $noun }} yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
