@servers(['localhost' => ['127.0.0.1']/*, 'prod' => ['user@ip']*/])

@story('deploy', ['on' => 'prod'])
    cdprod
    git pull
    build
    reload-prod
@endstory

@story('install-prod', ['on' => 'prod'])
    cdprod
    init
    build
    caddy start --config Caddyfile.prod
@endstory

@story('install-dev', ['on' => 'localhost'])
    cdprod
    init
    build
    caddy start --config Caddyfile.dev
@endstory

@story('refresh-dev', ['on' => 'localhost'])
    refresh
@endstory

@story('refresh-prod', ['on' => 'prod'])
    cdprod
    refresh
@endstory

@task('build')
    composer install
    npm install
    npm run build
    [ ! -e rr ] && ./vendor/bin/rr get-binary -n
    touch database/database.sqlite
    php artisan migrate
@endtask

@task('init')
    php -r "file_exists('.env') || copy('.env.example', '.env');"
    php artisan key:generate --ansi
@endtask

@task('reload-dev' , ['on' => 'localhost'])
    caddy reload --config Caddyfile.dev
    php artisan octane:reload
@endtask

@task('reload-prod', ['on' => 'prod'])
    cdprod
    caddy reload --config Caddyfile.prod
    php artisan octane:reload
@endtask

@task('refresh')
    composer dumpautoload
    php artisan clear-compiled
    php artisan optimize:clear
    php artisan config:clear
    php artisan view:clear
    php artisan route:clear
    php artisan cache:clear
    php artisan event:clear
    php artisan optimize
    php artisan config:cache
    php artisan view:cache
    php artisan route:cache
    php artisan event:cache
@endtask

@task('cdprod')
    {{--  CD to prod dir  --}}
@endtask
