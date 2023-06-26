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
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/mneme-kai-nous');

// Hooks

after('deploy:failed', 'deploy:unlock');
