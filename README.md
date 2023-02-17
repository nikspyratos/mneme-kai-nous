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
