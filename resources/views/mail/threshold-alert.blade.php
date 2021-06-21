@component('mail::message')
# Hi {{ $notifiable->name }}

{{ $trend_symbol }} Over the past {{ config('app.cache_ttls.threshold_alert_notification') / 60 / 60 }} hours,
{{ $coin_external_id }} has gone {{ $trend === true ? 'over' : 'under' }} your threshold of {{ $threshold }}.

{{ $coin_external_id }} value is {{ $value }}

All prices are in {{ strtoupper($currency_symbol) }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
