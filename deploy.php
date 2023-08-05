<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/crontab.php';

// Config

set('repository', 'git@github.com:nikspyratos/mneme-kai-nous.git');

add('shared_files', ['database/database.sqlite']);
add('shared_dirs', []);
add('writable_dirs', ['database']);

// Hosts

host('13.246.221.228')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/mneme-kai-nous.ankyr.dev')
    ->set('ssh_arguments', ['-i ~/.ssh/mkn_aws.pem'])
    ->set('forward_agent', false);

task('composer', function () {
    run('cd ~/mneme-kai-nous.ankyr.dev/current && composer install');
});

task('npm', function () {
    run('cd ~/mneme-kai-nous.ankyr.dev/current && npm install');
    run('cd ~/mneme-kai-nous.ankyr.dev/current && npm run build');
});

add('crontab:jobs', [
    '* * * * * cd {{current_path}} && {{bin/php}} artisan schedule:run >> /dev/null 2>&1',
]);

// Hooks
after('deploy:success', 'crontab:sync');

after('deploy:failed', 'deploy:unlock');
