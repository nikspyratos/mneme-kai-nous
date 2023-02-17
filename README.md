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

```
composer run-script refresh
```
