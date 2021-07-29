<?php namespace Pinacono\Backup\Events;

use Pinacono\Backup\Tasks\Monitor\BackupDestinationStatus;

class UnhealthyBackupWasFound {
  public BackupDestinationStatus $backupDestinationStatus;

  public function __construct( BackupDestinationStatus $backupDestinationStatus ) {
    $this->backupDestinationStatus = $backupDestinationStatus;
  }
}
