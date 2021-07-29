<?php namespace Pinacono\Backup\Events;

use Pinacono\Backup\BackupDestination\BackupDestination;

class BackupWasSuccessful {
  public BackupDestination $backupDestination;

  public function __construct( BackupDestination $backupDestination ) {
    $this->backupDestination = $backupDestination;
  }
}
