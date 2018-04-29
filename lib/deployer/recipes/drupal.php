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
task('drupal:env-settings', function () {

  $settingFileCustom = get('env_settings_file_custom');
  $settingsFileBaseLocation = get('env_settings_file_location_base');

  if(empty($settingFileCustom)) {
    $hostingProvider = get('hosting_provider');
    writeLn('No custom settings file selected, defaulting to basic file for: ' . $hostingProvider);
    $settingsFile = $settingsFileBaseLocation . '/settings.' . $hostingProvider . '.php';
  }
  else {
    $settingsFile = $settingFileCustom;
  }

  // Check or the file actually exists
  if(!file_exists($settingsFile)) {
    throw new \Exception('Settings file could not be found at: '.  $settingsFile);
  }

  // If all is well, copy over the file
  runLocally('mkdir -p ' . getcwd() . '/{{web_dir}}/sites/{{drupal_site_dir}}');
  runLocally('cp ' . $settingsFile . ' ' . getcwd() . '/{{web_dir}}/sites/{{drupal_site_dir}}/settings.env.php');
});

/**
 * Place a settings.local.php file into the needed sites/sitename dir.
 * This will allow extra non sensitive data to be deployed smoothly.
 */
desc('Get local settings file required for the stage.');
task('drupal:local-settings', function () {

  $settingFile =  get('local_settings_file');
  $settingFile = 'etc/' . $settingFile;

  // Check or the file actually exists
  if(!file_exists($settingFile)) {
    throw new \Exception('Local settings file could not be found at: '.  $settingFile);
  }

  // If all is well, copy over the file
  runLocally('mkdir -p ' . getcwd() . '/{{web_dir}}/sites/{{drupal_site_dir}}');
  runLocally('cp ' . $settingFile . ' ' . getcwd() . '/{{web_dir}}/sites/{{drupal_site_dir}}/settings.local.php');
});


/**
 * Symlink a sites/sitename folder to the sites/default folder if needed.
 */
desc('Symlink the default folder to a more specific site folder if needed.');
task('drupal:symlink', function () {

  // @TODO ensure this doesn't fail when the symlink already exists in the repo
  $siteDir = get('drupal_site_dir');
  $webDir = get('web_dir');

  if(!file_exists(getcwd() . '/' . $webDir . '/sites/default')) {
    writeLn('Symlinking "sites/default" folder to "sites/' . $siteDir . '"');
    runLocally('cd {{web_dir}}/sites && ln -s {{drupal_site_dir}} default');
  }
});