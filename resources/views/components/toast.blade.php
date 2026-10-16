<div class="toast-stack" data-toast-stack>
    @if (session()->has('toast'))
        <div class="toast {{ session('toast_ok', true) ? 'is-ok' : 'is-bad' }}" data-toast role="status">{{ session('toast') }}</div>
    @endif
    @if (($withErrors ?? false) && $errors->any())
        <div class="toast is-bad" data-toast role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
