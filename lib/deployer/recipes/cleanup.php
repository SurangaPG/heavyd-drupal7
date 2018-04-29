<?php
/**
 * @file Contains the needed extra items to the cleaning up of releases.
 */
namespace Deployer;

desc('unlock the needed site dirs for the releases to be cleaned up');
task('cleanup:unlock', function () {
  $releases = get('releases_list');
  $keep = get('keep_releases');
  if ($keep === -1) {
    // Keep unlimited releases.
    return;
  }
  while ($keep - 1 > 0) {
    array_shift($releases);
    --$keep;
  }
  foreach ($releases as $release) {
    run("chmod -R u+w {{deploy_path}}/releases/$release");
  }
});