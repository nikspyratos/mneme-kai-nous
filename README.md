# Mneme kai Nous

Personal automation

## Install

### Dev
```
composer run-script setup-dev 
```

### Prod

```
composer run-script setup-prod 
```

## Deploy (Prod-only)

```
composer run-script deploy
```

## Troubleshooting

### Clear & refresh everything

- Dumps Composer autoload
- Removes compiled class file
- Clears & re-caches config, view, route, event and general cache

```
composer run-script refresh
```
