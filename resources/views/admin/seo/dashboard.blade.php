@extends('layouts.admin')

@section('content')
    <section class="panel-head">
        <div>
            <h1>SEO</h1>
            <p>Active records that still need meta tags. The public sitemap is at <a href="{{ route('sitemap') }}">/sitemap.xml</a>.</p>
        </div>
    </section>
    <section class="metric-grid">
        <article class="panel metric">
            <p>Courses</p>
            <strong>{{ $missing['courses'] }}</strong>
            <span>Active courses without SEO meta</span>
        </article>
        <article class="panel metric">
            <p>Services</p>
            <strong>{{ $missing['services'] }}</strong>
            <span>Active services without SEO meta</span>
        </article>
        <article class="panel metric">
            <p>Pages</p>
            <strong>{{ $missing['pages'] }}</strong>
            <span>Active pages without SEO meta</span>
        </article>
        <article class="panel metric">
            <p>Blogs</p>
            <strong>{{ $missing['blogs'] }}</strong>
            <span>Active articles without SEO meta</span>
        </article>
    </section>
    <section class="panel form-panel">
        <h2>Redirects</h2>
        <form method="post" action="{{ route('admin.seo.redirects.store') }}">
            @csrf
            <div class="form-grid">
                <label class="field"><span>From</span><input class="input" name="from_path" value="{{ old('from_path') }}" placeholder="/old-path" required></label>
                <label class="field"><span>To</span><input class="input" name="to_path" value="{{ old('to_path') }}" placeholder="/courses" required></label>
                <label class="field"><span>Code</span>
                    <select class="select" name="status_code">
                        <option value="301">301</option>
                        <option value="302">302</option>
                    </select>
                </label>
            </div>
            <div class="form-actions"><button class="btn btn-purple" type="submit">Save redirect</button></div>
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>From</th><th>To</th><th>Code</th><th></th></tr></thead>
                <tbody>
                    @forelse ($redirects as $redirect)
                        <tr>
                            <td>{{ $redirect->from_path }}</td>
                            <td>{{ $redirect->to_path }}</td>
                            <td>{{ $redirect->status_code }}</td>
                            <td>
                                <form method="post" action="{{ route('admin.seo.redirects.destroy', $redirect) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="act act-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No redirects yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
