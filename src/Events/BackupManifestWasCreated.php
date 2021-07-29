<?php namespace Pinacono\Backup\Events;

use Pinacono\Backup\Tasks\Backup\Manifest;

class BackupManifestWasCreated {
  public Manifest $manifest;

  public function __construct( Manifest $manifest ) {
    $this->manifest = $manifest;
  }
}
