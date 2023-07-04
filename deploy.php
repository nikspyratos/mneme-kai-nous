<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github.com:nikspyratos/mneme-kai-nous.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('207.154.230.127')
    ->set('remote_user', 'forge')
    ->set('deploy_path', '~/mneme-kai-nous.ankyr.dev')
    ->set('ssh_arguments', ['-i ~/.ssh/id_rsa_mkn']);

task('composer', function () {
    run('cd ~/mneme-kai-nous.ankyr.dev && composer install');
});

task('npm', function () {
    run('cd ~/mneme-kai-nous.ankyr.dev && npm install');
    run('cd ~/mneme-kai-nous.ankyr.dev && npm run build');
});

// Hooks

after('deploy:failed', 'deploy:unlock');
