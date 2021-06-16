# Crypto Coin Watcher

Crypto Coin Watcher is an application that sends you alerts about prices and averages of crypto currencies.

## Requirements

- PHP 8.0: on macOS you can install it via Homebrew: `brew install php@8.0`;
- Composer 2: see the [Composer documentation](https://getcomposer.org/download/) for instructions to install it

## Functionality

Crypto Coin Watcher has 3 types of alerts available:

- Regular alerts (daily or weekly): send an email about the trend and min and max value of a set of crypto currencies, for a past period of time;
- Threshold alert: send an email when a crypto currency goes above or below a certain configured fiat currency threshold;
- Variation alert: send an email whenever a crypto currency has a growth of a certain percentage in the past 2h;

## Technologies

This application leverages the [CoinGeck API](https://www.coingecko.com/api/documentations/v3#/) to fetch crypto currency data, Laravel Horizon queue manager to offload queue jobs (especially around communicating with the CoinGecko API) and the Laravel Scheduler for regular work like fetching the current currency value every 2 minutes
