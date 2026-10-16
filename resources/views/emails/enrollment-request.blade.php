New course enrollment request from the website.

@foreach ($details as $label => $value)
{!! $label !!}: {!! filled($value) ? $value : '-' !!}
@endforeach

Message:
{!! filled($context) ? $context : '-' !!}

Enquiry #{{ $enquiry->id }}, received {{ $enquiry->created_at?->format('j M Y, g:i A') }}.
