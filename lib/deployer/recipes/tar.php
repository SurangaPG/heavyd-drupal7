<?php
/**
 * @file Contains the needed extra items to generate, sync, untar and cleanup
 *  a tar archive.
 */
namespace Deployer;

use Deployer\Task\Context; // This is loaded in by the deployer.phar and is accessible

set('tar_ignore', [
  '.workflow',
  '.git',
  '.platform'
]);

set('tar_workspace', getcwd());

desc('Tar all the documents in the document root.');
task('tar:archive', function () {

  $src = get('tar_workspace');

  // Glob all the files in the current dir for tarring by default
  $glob = glob($src . '/*');
  $tar_files = [];
  $ignored_files = get('tar_ignore');

  // Exclude all the items from the tar that are in the ignore list.
  foreach($glob as $file) {

    // Make the path relative
    $file = str_replace($src . '/', '', $file);

    if(!in_array($file , $ignored_files)) {
      $tar_files[] = $file;
    }
  }
  runlocally('tar -cvf archive.tar ' . implode(' ', $tar_files));
});


desc('Untar all the files synced');
task('tar:un-archive', function () {
  cd('{{release_path}}');
  run('tar -xvf archive.tar ');
});


desc('sftp over the tar to the release location.');
task('tar:sftp', function () {

  $server = Context::get()->getServer();

  $releasePath = get('release_path');
  // Upload the actual file
  $server->upload('archive.tar', $releasePath . '/archive.tar');
});

desc('Cleanup all the tar files used');
task('tar:cleanup', function () {
  cd('{{release_path}}');
  run('rm -rf archive.tar ');
  runLocally('rm -rf {{tar_workspace}}/archive.tar ');
});