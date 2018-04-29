<?php
/**
 * @file Contains some useful helpers for common drush commands.
 */
namespace Deployer;

set('drush_bin', 'drush');

desc('Drush activate maintenance mode');
task('drush:maintenance-mode-activate', function () {
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');
  run('{{drush_bin}} state-set maintenance_mode 1');
});

desc('Drush deactivate maintenance mode');
task('drush:maintenance-mode-deactivate', function () {
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');
  run('{{drush_bin}} state-set maintenance_mode 0');
});

desc('Drush cache rebuild');
task('drush:cache-rebuild', function () {
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');
  run('{{drush_bin}} cache-rebuild');
});

// Import all the config
desc('Drush config import');
task('drush:config-import', function () {
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');
  run('{{drush_bin}} config-import -y');
});

// Import update all the entities
desc('Drush entity updates');
task('drush:entity-updates', function () {
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');
  run('{{drush_bin}} entity-updates -y');
});

// Import update all the entities
desc('Drush database updates');
task('drush:updatedb', function () {
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');
  run('{{drush_bin}} updatedb -y');
});

// Run cron
desc('Drush cron');
task('drush:cron', function () {
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');
  run('{{drush_bin}} cron -y');
});

// Enables stage file proxy
// @TODO Add support for hotlinking. The setting doesn't appear to be part of state.
desc('Drush Stage file proxy');
task('drush:stage-file-proxy', function () {
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');

  // Check or the needed vars are set
  $stageFileProxySource = get('stage_file_proxy_source');
  $stageFileProxySource = rtrim('/', $stageFileProxySource); // Trim of trailing slashes as stage_file_proxy doesn't allow those.)

  $stageFileProxyActive = get('stage_file_proxy_active');

  if($stageFileProxyActive && !empty($stageFileProxySource)) {
    run('{{drush_bin}} en stage_file_proxy -y');
    run('{{drush_bin}} state-set stage_file_proxy_origin "' . $stageFileProxySource . '"');
  }
});

/**
 * Make a fresh install for the drupal site.
 * Depending on what we're doing exactly this can either be the initial set up
 * of a production environment. Or just the building of a test env.
 */
desc('Installs a drupal site, usually this is only done once for production but can be done any number of times.');
task('drush:site-install', function () {
  writeLn('Installing drupal');
  cd('{{release_path}}/{{web_dir}}/sites/{{drupal_site_dir}}');
  run('chmod -R u+w {{release_path}}/{{web_dir}}');
  run('drush site-install -y config_installer --sites-subdir={{ drupal_site_dir }}');
  run('drush status -y');
});