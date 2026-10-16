@extends('layouts.admin')

@section('content')
    @include('admin.courses.partials.head', [
        'heading' => 'Bulk upload questions',
        'back' => route('admin.courses.questions', [$course->id, $quiz->id]),
        'backLabel' => $quiz->title,
    ])
    <form class="panel form-panel" method="post" action="{{ route('admin.courses.questions.import', [$course->id, $quiz->id]) }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <label class="field span-2">
                <span>CSV file<i class="req">*</i></span>
                <span class="field-help">Up to {{ number_format($maxRows) }} questions per file, maximum 5 MB. If any row has a problem nothing is imported, and the row numbers to fix are listed. New questions are added after the existing ones.</span>
                <input class="input" type="file" name="file" accept=".csv,text/csv" required>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">Upload questions</button>
            <a class="btn btn-outline" href="{{ route('admin.courses.questions.template', [$course->id, $quiz->id]) }}">Download CSV template</a>
        </div>
    </form>

    <section class="material-block">
        <div class="panel-head">
            <div>
                <h2>How to fill the file</h2>
                <p>Start from the template, keep the heading row, and add one question per row. In Excel, save with File &gt; Save As &gt; CSV UTF-8.</p>
            </div>
        </div>
        <div class="panel list-panel">
            <div class="table-wrap">
                <table class="import-guide">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>What to enter</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>type</code></td>
                            <td><code>mcq</code>, <code>multiple</code>, <code>true_false</code>, <code>scenario</code>, <code>fill_blank</code>, <code>short</code> or <code>essay</code>. Empty means <code>mcq</code>.</td>
                        </tr>
                        <tr>
                            <td><code>question</code></td>
                            <td>The question text. Required.</td>
                        </tr>
                        <tr>
                            <td><code>scenario</code></td>
                            <td>The situation the learner reads first. Required for <code>scenario</code>, ignored otherwise.</td>
                        </tr>
                        <tr>
                            <td><code>option_1</code> to <code>option_6</code></td>
                            <td>Answer choices. Choice questions need at least two. For <code>fill_blank</code> and <code>short</code>, each filled option is an accepted answer. Leave empty for <code>true_false</code> and <code>essay</code>.</td>
                        </tr>
                        <tr>
                            <td><code>correct</code></td>
                            <td>The correct option as a letter or number, for example <code>B</code> or <code>2</code>. For <code>multiple</code>, separate them with commas: <code>A,C</code>. For <code>true_false</code>, write <code>True</code> or <code>False</code>.</td>
                        </tr>
                        <tr>
                            <td><code>marks</code></td>
                            <td>Points for a correct answer. Empty means 1.</td>
                        </tr>
                        <tr>
                            <td><code>negative_marks</code></td>
                            <td>Points removed for a wrong answer. Empty means 0.</td>
                        </tr>
                        <tr>
                            <td><code>explanation</code></td>
                            <td>Shown to the learner in the results review. Optional.</td>
                        </tr>
                        <tr>
                            <td><code>status</code></td>
                            <td><code>active</code> or <code>inactive</code>. Empty means active.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
