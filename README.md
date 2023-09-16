# Mneme kai Nous

This project was my personal finance & small scale personal automation repository. It is now public for interest purposes, as I've moved my budgeting elsewhere.

It can:
- Track Investec transactional data via the API, by using [my SDK](https://github.com/nikspyratos/investec-sdk-php)
- Track Woolworths credit card and Absa card transactions via [SMS interception](https://writing.nikspyratos.com/Writing/2023/07-18+Build+Your+Own+Budgeting+-+How+to+track+your+transactions+automatically+from+APIs+and+your+phone)
- Track Loadshedding with the [ESP API](https://eskomsepush.gumroad.com/)
- Life percentage tracker
- Random quotes from quote archives

Made to run with Deployer.

## Install

### Dev
```
php vendor/bin/envoy run install-dev
```

### Prod

```
php vendor/bin/envoy run install-dev

```

## Deploy (Prod-only)

```
php vendor/bin/envoy run deploy
```

## Troubleshooting

### Clear & refresh everything

- Dumps Composer autoload
- Removes compiled class file
- Clears & re-caches config, view, route, event and general cache

## TODO

- Install https://github.com/Wulfheart/laravel-actions-ide-helper once it supports Laravel 10
- Get Vite + HMR working with Caddy
  - https://github.com/nuxt/nuxt/issues/12748
  - https://github.com/vitejs/vite/discussions/6473

## Exposing the API publicly

To test Investec's programmable banking events, you need a publicly accessible event.

1. Install https://expose.dev/ & follow initial steps for token & server selection
2. Run Octane `php artisan octane:start --watch`
3. Run Expose `expose share https://localhost --subdomain=nikolaos-spyratos`

The service should now be accessible on the web on the domain provided to you by Expose. Use this for programmable banking testing.

## Programmable Banking

### Sample Transaction data

```json
{
    "accountNumber": "10013240747",
    "dateTime": "2023-03-12T11:28:40.916Z",
    "centsAmount": 10000,
    "currencyCode": "zar",
    "type": "card",
    "reference": "simulation",
    "card": {
        "id": "4208ae29d5beb36a991938486547b74db61a517616cd6eeb53a7ee68418b992c"
    },
    "merchant": {
        "category": {
            "code": "5462",
            "key": "bakeries",
            "name": "Bakeries"
        },
        "name": "The Coders Bakery",
        "city": "Cape Town",
        "country": {
            "code": "ZA",
            "alpha3": "ZAF",
            "name": "South Africa"
        }
    }
}
```
