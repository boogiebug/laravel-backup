<?php namespace Pinacono\Backup\Exceptions;

use Exception;
use Pinacono\Backup\BackupDestination\Backup;

class InvalidBackupFile extends Exception {
  public static function writeError(string $backupName): self  {
    return new static("There has been an error writing file for the backup named `{$backupName}`.");
  }

  public static function readError(Backup $backup): self {
    $path = $backup->path();
    return new static("There has been an error reading the backup `{$path}`");
  }
}
