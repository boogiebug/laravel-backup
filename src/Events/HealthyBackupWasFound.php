<?php namespace Pinacono\Backup\Events;

use Pinacono\Backup\Tasks\Monitor\BackupDestinationStatus;

class HealthyBackupWasFound {
  public BackupDestinationStatus $backupDestinationStatus;

  public function __construct(BackupDestinationStatus $backupDestinationStatus) {
    $this->backupDestinationStatus = $backupDestinationStatus;
  }
}
