@component('mail::message')
# Hi {{ $notifiable->name }}

Here's your daily update {{ config('app.name') }},
from {{ $start->toDateTimeString() }} to {{ $end->toDateTimeString() }}.

@component('mail::table')
| Coin (Currency) | Min | Max | Start | End |
|-----------------|-----|-----|-------|-----|
@foreach ($data as $row)
| {{ $row['coin'] }} ({{ $row['currency'] }}) | {{ $row['min'] }} | {{ $row['max']}} | {{ $row['first'] }} | {{ $row['last'] }} |
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
