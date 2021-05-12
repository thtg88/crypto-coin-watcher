@component('mail::message')
# Hi {{ $notifiable->name }}

Here's your daily {{ config('app.name') }} update,
from {{ $start->toDateTimeString() }} to {{ $end->toDateTimeString() }}.

## Average

@component('mail::table')
| Coin | Trend | Start | End |
|------|:-----:|------:|----:|
@foreach ($data as $row)
| {{ $row['coin'] }} | {{ $row['trend'] }} | {{ $row['first'] }} | {{ $row['last'] }} |
@endforeach
@endcomponent

## Min-Max

@component('mail::table')
| Coin | Min | Max |
|------|----:|----:|
@foreach ($data as $row)
| {{ $row['coin'] }} | {{ $row['min'] }} | {{ $row['max']}} |
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
