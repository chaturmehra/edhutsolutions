@extends('layouts.admin')

@section('content')
    @include('admin.courses.partials.head', [
        'heading' => $quiz->title,
        'back' => route('admin.courses.materials', $course->id),
        'backLabel' => 'Course materials',
    ])
    <section class="material-block">
        <div class="panel-head">
            <div>
                <h2>Questions</h2>
                <p>
                    {{ $quiz->kindLabel() }}.
                    Pass mark {{ rtrim(rtrim(number_format((float) $quiz->pass_percentage, 2), '0'), '.') }}%. {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes.' minute limit.' : 'No time limit.' }}
                    @if ($quiz->isMock())
                        {{ $quiz->questions_per_attempt ? $quiz->questions_per_attempt.' random questions per attempt.' : 'Every active question per attempt, in a random order.' }}
                    @endif
                </p>
            </div>
            <div class="form-actions">
                <a class="btn btn-outline" href="{{ route('admin.courses.quizzes.edit', [$course->id, $quiz->id]) }}">Edit quiz</a>
                <a class="btn btn-outline" href="{{ route('admin.courses.questions.import.form', [$course->id, $quiz->id]) }}">Bulk upload</a>
                <a class="btn btn-blue" href="{{ route('admin.courses.questions.create', [$course->id, $quiz->id]) }}">+ Add question</a>
            </div>
        </div>
        <div class="panel list-panel">
            <div class="table-wrap">
                <table class="question-table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Marks</th>
                            <th>Options</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quiz->questions as $question)
                            <tr>
                                <td>{{ $question->prompt }}</td>
                                <td>{{ \App\Support\QuestionTypes::label($question->type) }}</td>
                                <td>{{ rtrim(rtrim(number_format((float) $question->marks, 2), '0'), '.') }}</td>
                                <td>{{ $question->options->count() }}</td>
                                <td><span class="tag {{ $question->status === 'active' ? '' : 'is-off' }}">{{ ucfirst($question->status) }}</span></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="act" href="{{ route('admin.courses.questions.edit', [$course->id, $quiz->id, $question]) }}">Edit</a>
                                        <form method="post" action="{{ route('admin.courses.questions.destroy', [$course->id, $quiz->id, $question]) }}" data-confirm="Delete this question?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="act act-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">Add the first question for this quiz.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
