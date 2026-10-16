New corporate training enquiry from the website.

@foreach ($details as $label => $value)
{!! $label !!}: {!! filled($value) ? $value : '-' !!}
@endforeach

Context and goals:
{!! filled($context) ? $context : '-' !!}

Enquiry #{{ $enquiry->id }}, received {{ $enquiry->created_at?->format('j M Y, g:i A') }}.
