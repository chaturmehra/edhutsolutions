<div class="table-scroll">
    <table class="learn-table learn-list">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Duration</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($videos as $video)
                @php($player = $video->player())
                <tr>
                    <td class="learn-num" data-label="#">{{ $loop->iteration }}</td>
                    <td class="learn-name" data-label="Title"><strong>{{ $video->title }}</strong></td>
                    <td data-label="Duration">{{ $video->duration ?: '—' }}</td>
                    <td data-label="Action">
                        @if ($player)
                            <button class="learn-view" type="button" data-video-open data-video-type="{{ $player['type'] }}" data-video-src="{{ $player['src'] }}" data-video-title="{{ $video->title }}" @if ($player['protected']) data-video-protected @endif>View</button>
                        @elseif ($video->external_url)
                            <a class="learn-view" href="{{ $video->external_url }}" target="_blank" rel="noopener">Open link</a>
                        @else
                            <span class="muted">Not available</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
