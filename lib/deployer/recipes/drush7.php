<?php
/**
 * @file Contains some useful helpers for common drush commands.
 */
namespace Deployer;

set('drush_bin', 'drush');

desc('Drush activate maintenance mode');
task('drush7:maintenance-mode-activate', function () {
  cd('{{release_path}}/web/sites/{{site}}');
  run('{{drush_bin}} var-set maintenance_mode 1');
});

desc('Drush deactivate maintenance mode');
task('drush7:maintenance-mode-deactivate', function () {
  cd('{{release_path}}/web/sites/{{site}}');
  run('{{drush_bin}} var-set maintenance_mode 0');
});

desc('Drush cache clear');
task('drush7:cache-clear', function () {
  cd('{{release_path}}/web/sites/{{site}}');
  run('{{drush_bin}} cache-clear all');
});

// Import all the config
desc('Drush feature revert all');
task('drush7:feature-revert-all', function () {
  cd('{{release_path}}/web/sites/{{site}}');
  run('{{drush_bin}} feature-revert-all -y');
});

// Import update all the entities
desc('Drush database updates');
task('drush7:updatedb', function () {
  cd('{{release_path}}/web/sites/{{site}}');
  run('{{drush_bin}} updatedb -y');
});

// Run cron
desc('Drush cron');
task('drush7:cron', function () {
  cd('{{release_path}}/web/sites/{{site}}');
  run('{{drush_bin}} cron -y');
});
