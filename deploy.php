<?php
namespace Deployer;

require 'recipe/common.php';

// Configuration

set('repository', 'git@github.com:beer/lottery.git');
//set('git_tty', true); // [Optional] Allocate tty for git on first deployment
set('shared_files', ['config/database.json']);
set('shared_dirs', []);
set('writable_dirs', []);
set('default_stage', 'prod');


// Hosts

host('speculator.im')
    ->user('deployer')
    ->stage('prod')
    ->set('deploy_path', '/srv/www/lottery');
    
desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    //'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
