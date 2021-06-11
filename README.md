# Crypto Coin Watcher

Crypto Coin Watcher is an application that fetches prices of crypto currencies and calculates averages and sends alerts to users.

## Requirements

- PHP 8.0
- Composer 2

## Functionality

Crypto Coin Watcher has 3 types of alerts available:

- Regular alerts (daily or weekly): send an email about the trend and min and max value of a set of crypto currencies, from the past period of time
- Threshold alert: send an email when a crypto currency goes above or below a certain configured fiat currency threshold
- Variation alert: send an email whenever a crypto currency has a growth of a certain percentage in the past 2h

## Technologies

This application leverages the [CoinGeck API](https://www.coingecko.com/api/documentations/v3#/) to fetch crypto currency data, Laravel Horizon queue manager to offload queue jobs (especially around communicating with the CoinGecko API) and the Laravel Scheduler for regular work like fetching the current currency value every 2 minutes
