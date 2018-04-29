<?php
/*
 * Get a full Drupal 8 build process.
 */

namespace Deployer;

use Deployer\Task\Context; // This is loaded in by the deployer.phar and is accessible

# Dependencies from the standard deployer phar
require 'recipe/common.php';

# Dependencies from the extra deployer recipes
require __DIR__ . '/../../../../deployer/recipes/local.php';
require 'recipes/tar.php';
require 'recipes/cleanup.php';

# Extra task requirements
require 'recipes/drush7.php';
require 'recipes/drupal7.php';

# Set some standard config.
set('env_settings_file_location_base', '.deployer/vendor/oneagency/deployer/drupal/7/settings');
set('env_settings_file_custom', null);

# The local settings file used by this server.
set('local_settings_file', null);

set('drush_bin', 'drush');
set('local_release_path', '{{ workspace }}');

# set('ssh_type', 'native');
# set('ssh_multiplexing', true);

set('tar_ignore', [
  '.workflow',
  '.git',
  '.platform',
  'phpcs-rulset.xml.dist',
  'phpunit.xml.dist',
  '.travis.yml',
  '.workflow.yml',
  '.bitbucket-pipelines.yml',
  'temp',
  'tests',
  'patches',
  'command',
  'artifact',
  '.deployer',
]);

set('keep_releases', 3);

set('shared_files', []);

# Standard step requirements
/**
 * Runs a full deploy
 */
desc('Run a full deploy from start to end. Building all the code on the build server and tarring it afterwards');
task('deploy', [
  // Pre release step
  'deploy:prepare', // Prepares some symlinks etc
  'deploy:lock', // Locks this deploy (prevents several deploys running together)
  'deploy:release', // Count the release

  // Build step
  'drupal7:env-settings', // Get the correct settings.local file
  'drupal7:local-settings', // Get the correct settings.local file
  'drupal7:symlink', // Place the needed symlink

  // Sync step
  'tar:archive',
  'tar:sftp',
  'tar:un-archive',
  'tar:cleanup',

  // Deploy step
  'deploy:shared',
//  'drush7:cache-clear',
//  'drush7:feature-revert-all',
//  'drush7:updatedb',
//  'drush7:cron',
  'deploy:symlink',

  // Clean up step
  'deploy:unlock',
  'cleanup:unlock', // @TODO define this a as a before task.
  'cleanup'
]);
