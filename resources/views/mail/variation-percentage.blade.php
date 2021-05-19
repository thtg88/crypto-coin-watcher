@component('mail::message')
# Hi {{ $notifiable->name }}

The following variation in the past {{ \App\Caches\VariationPercentageNotificationCache::TTL / 60 / 60 }} hours has triggered your alert.

## Variation

@component('mail::table')
| Coin | Variation | Current Avg Value |
|------|----------:|------------------:|
| {{ $coin_external_id }} | {{ $variation_percentage.'% '.$trend }} | {{ $final_value }} |
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
