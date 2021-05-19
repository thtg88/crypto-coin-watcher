@component('mail::message')
# Hi {{ $notifiable->name }}

The following variation in the past {{ VariationPercentageNotificationCache::TTL / 60 / 60 }} hours has triggered your alert.

## Variation

@component('mail::table')
| Coin | Variation |
|------|----------:|
| {{ $coin_external_id }} | {{ $variation_percentage.'% '.$trend }} |
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
