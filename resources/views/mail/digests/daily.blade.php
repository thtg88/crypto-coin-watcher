@component('mail::message')
# Hi {{ $notifiable->name }}

Here's your daily update {{ config('app.name') }},
from {{ $start->toDateTimeString() }} to {{ $end->toDateTimeString() }}.

@component('mail::table')
| Coin | Min | Max | Start | End | Trend |
|------|----:|----:|------:|----:|:-----:|
@foreach ($data as $row)
| {{ $row['coin'] }} | {{ $row['min'] }} | {{ $row['max']}} | {{ $row['first'] }} | {{ $row['last'] }} | {{ $row['trend'] }}
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
