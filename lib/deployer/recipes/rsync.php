<?php
/**
 * @file Contains the needed extra items to generate, sync, untar and cleanup
 *  a tar archive.
 */
namespace Deployer;

use Deployer\Task\Context; // This is loaded in by the deployer.phar and is accessible

set('rsync_options', [
  'options' => [
    '--exclude=.git',
    '-q',
    // @TODO Fix this in a safer/cleaner way.
    '-e "ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no"
    -r'
  ],
]);

set('rsync_buildspace', getcwd() . '/');

desc('rsync the files over to the final location.');
task('rsync:sync', function () {
  $releasePath = get('release_path') . '/';
  $sourcePath = get('rsync_buildspace');

  // Upload the actual file
  writeln('syncing from ' . $sourcePath . ' to ' . $releasePath);
  upload($sourcePath, $releasePath, get('rsync_options'));
});

/**
 * Rebases the project phing data etc in such a way that it is compatible with
 * the final location of the project. This will skip out any hardcoded paths
 * set to the project buildspace and set them to match the project deploy space.
 */
desc('Makes all the files read only and ready for transfer.');
task('rsync:lock', function() {
  runLocally("chmod -R o-w {{local_workspace}}/web");
});
