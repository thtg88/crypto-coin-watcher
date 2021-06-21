@component('mail::message')
# Hi {{ $notifiable->name }}

{{ $trend_symbol }} Over the past {{ $seconds_between_alerts / 60 / 60 }} hours,
{{ $coin_external_id }} has gone {{ $trend === true ? 'over' : 'under' }} your threshold of {{ $threshold }}.

{{ $coin_external_id }} value is {{ $value }}

All prices are in {{ strtoupper($currency_symbol) }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
