<?php namespace Pinacono\Backup\Tasks\Cleanup;

use Illuminate\Contracts\Config\Repository;
use Pinacono\Backup\BackupDestination\BackupCollection;
use Pinacono\Backup\BackupDestination\BackupDestination;

abstract class CleanupStrategy {
  protected BackupDestination $backupDestination;
  protected Repository $config;

  public function __construct( Repository $config ) {
    $this->config = $config;
  }

  abstract public function deleteOldBackups(BackupCollection $backups);

  public function setBackupDestination(BackupDestination $backupDestination): self {
    $this->backupDestination = $backupDestination;
    return $this;
  }

  public function backupDestination(): BackupDestination {
    return $this->backupDestination;
  }
}
