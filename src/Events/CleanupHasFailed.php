<?php namespace Pinacono\Backup\Events;

use Exception;
use Pinacono\Backup\BackupDestination\BackupDestination;

class CleanupHasFailed {
  public Exception $exception;
  public ?BackupDestination $backupDestination = null;

  public function __construct(Exception $exception, ?BackupDestination $backupDestination = null) {
    $this->exception = $exception;
    $this->backupDestination = $backupDestination;
  }
}
