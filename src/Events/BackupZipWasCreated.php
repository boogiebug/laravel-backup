<?php namespace Pinacono\Backup\Events;

class BackupZipWasCreated {
  public string $pathToZip;

  public function __construct(string $pathToZip) {
    $this->pathToZip = $pathToZip;
  }
}
