<?php
/**
 * @file Contains the needed extra items to handle drupal specific builds
 */
namespace Deployer;

/**
 * Place a settings.env.php file into the needed sites/sitename dir.
 * This will be selected based on the host for the site. E.g; The kanooh version
 * will read out the specific kanooh env data etc.
 *
 * Custom files can be specified if needed by the project.
 */
desc('Get the env settings file needed for the hosting provider.');
task('drupal7:env-settings', function () {

  $environment = get('environment');
  $settingsFile = getcwd() . '/etc/env/' . $environment . '/settings.env.php';

  // Check or the file actually exists
  if(!file_exists($settingsFile)) {
    throw new \Exception('Settings file could not be found at: '.  $settingsFile);
  }

  // If all is well, copy over the file
  runLocally('mkdir -p ' . getcwd() . '/web/sites/{{site}}');
  runLocally('cp ' . $settingsFile . ' ' . getcwd() . '/web/sites/{{site}}/settings.env.php');
});

/**
 * Place a settings.local.php file into the needed sites/sitename dir.
 * This will allow extra non sensitive data to be deployed smoothly.
 */
desc('Get local settings file required for the stage.');
task('drupal7:local-settings', function () {

  $settingFile =  get('local_settings_file');
  $settingFile = 'etc/' . $settingFile;

  // Check or the file actually exists
  if(!file_exists($settingFile)) {
    throw new \Exception('Local settings file could not be found at: '.  $settingFile);
  }

  // If all is well, copy over the file
  runLocally('mkdir -p ' . getcwd() . '/web/sites/{{site}}');
  runLocally('cp ' . $settingFile . ' ' . getcwd() . '/web/sites/{{site}}/settings.local.php');
});


/**
 * Symlink a sites/sitename folder to the sites/default folder if needed.
 */
desc('Symlink the default folder to a more specific site folder if needed.');
task('drupal7:symlink', function () {

  // @TODO ensure this doesn't fail when the symlink already exists in the repo
  $siteDir = get('site');
  $webDir = 'web';

  $defaultDir = getcwd() . '/' . $webDir . '/sites/default';

  // Since it's possible that the same code is used to build from the same
  // container multiple times we prevent the previous "default" dir from lingering.
  if (file_exists($defaultDir) && $siteDir != "default") {
    runLocally("rm -rf " . $defaultDir);
  }

  if(!file_exists(getcwd() . '/' . $webDir . '/sites/default')) {
    writeLn('Symlinking "sites/default" folder to "sites/' . $siteDir . '"');
    runLocally('cd web/sites && ln -s {{site}} default');
  }
});