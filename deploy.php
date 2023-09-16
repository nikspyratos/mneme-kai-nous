<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/crontab.php';

// Config

set('repository', 'git@github.com:nikspyratos/mneme-kai-nous.git');

add('shared_files', []);
add('shared_dirs', ['sqlite']);
add('writable_dirs', ['sqlite']);

// Hosts

host('YOURIPHERE')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/your_domain_here')
    ->set('ssh_arguments', ['-i ~/.ssh/your_key_here'])
    ->set('forward_agent', false);

task('composer', function () {
    run('cd ~/your_domain_here/current && composer install');
});

task('npm', function () {
    run('cd ~/your_domain_here/current && npm install');
    run('cd ~/your_domain_here/current && npm run build');
});

add('crontab:jobs', [
    '* * * * * cd {{current_path}} && {{bin/php}} artisan schedule:run >> /dev/null 2>&1',
]);

// Hooks
after('deploy:success', 'crontab:sync');
after('deploy:success', 'crontab:sync');

after('deploy:failed', 'deploy:unlock');
