<?php
namespace Deployer;

require 'recipe/yii2-app-advanced.php';
require 'recipe/rsync.php';

inventory('deployer/servers.yml');

// Project name
set('application', 'auto-loads');
set('rsync', [
    'exclude'       => [
        '.git',
        'deploy.php',
    ],
    'exclude-file'  => false,
    'include'       => [],
    'include-file'  => false,
    'filter'        => [],
    'filter-file'   => false,
    'filter-perdir' => false,
    'flags'         => 'rz', // Recursive, with compress
    'options'       => ['delete'],
    'timeout'       => 900,
]);
set('rsync_src', __DIR__ . '/../');
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');
set('shared_dirs', [
    'frontend/runtime',
    'backend/runtime',
    'console/runtime',
    'common/documents'
]);

task('fix-permissions', function () {
    run('cd {{release_path}} && find . -type d -exec chmod 755 {} ";"');
    run('cd {{release_path}} && find . -type f -exec chmod 644 {} ";"');
    run('chgrp www-data {{release_path}}/frontend/web/assets');
    run('chgrp www-data {{release_path}}/backend/web/assets');
    run('chmod 775 {{release_path}}/frontend/web/assets');
    run('chmod 775 {{release_path}}/backend/web/assets');
    write('Permissions fixed!');
});

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:vendors',
    'deploy:init',
    'deploy:shared',
    'deploy:run_migrations',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

after('deploy:run_migrations', 'fix-permissions');
after('deploy:failed', 'deploy:unlock');
