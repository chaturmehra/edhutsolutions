@if (session('status'))
    <div class="alert" role="status">{{ session('status') === 'verification-link-sent' ? 'A fresh verification link has been sent.' : session('status') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
