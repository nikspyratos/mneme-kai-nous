# Mneme kai Nous

Personal automation

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
