@if ($tests->isNotEmpty())
    @if ($note)
        <p class="muted learn-note">{{ $note }}</p>
    @endif
    <div class="table-scroll">
        <table class="learn-table learn-list">
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Duration (in Min.)</th>
                    <th>No Of Question</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tests as $test)
                    <tr>
                        <td class="learn-name" data-label="Test Name"><strong>{{ $test->title }}</strong></td>
                        <td data-label="Duration (in Min.)">{{ $test->time_limit_minutes ?: 'No limit' }}</td>
                        <td data-label="No Of Question">{{ $test->attemptSize((int) $test->question_count) }}</td>
                        <td data-label="Action">
                            <form method="post" action="{{ route('quizzes.start', $test) }}">
                                @csrf
                                <button class="learn-start" type="submit">{{ in_array($test->id, $openQuizIds) ? 'Resume' : 'Start' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="muted">{{ $empty }}</p>
@endif
