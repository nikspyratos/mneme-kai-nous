<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github.com:nikspyratos/mneme-kai-nous.git');

add('shared_files', []);
add('shared_dirs', ['database']);
add('writable_dirs', []);

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

task('cron:install', function() {
    run('crontab ~/mneme-kai-nous.ankyr.dev/current/cron');
});

// Hooks

after('deploy:failed', 'deploy:unlock');
