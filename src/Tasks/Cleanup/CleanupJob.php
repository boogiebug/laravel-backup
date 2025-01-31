<?php namespace Pinacono\Backup\Tasks\Cleanup;

use Exception;
use Illuminate\Support\Collection;

use Pinacono\Backup\BackupDestination\BackupDestination;
use Pinacono\Backup\Events\CleanupHasFailed;
use Pinacono\Backup\Events\CleanupWasSuccessful;
use Pinacono\Backup\Helpers\Format;

class CleanupJob {
  protected Collection $backupDestinations;
  protected CleanupStrategy $strategy;
  protected bool $sendNotifications = true;

  public function __construct(
    Collection $backupDestinations,
    CleanupStrategy $strategy,
    bool $disableNotifications = false
  ) {
    $this->backupDestinations = $backupDestinations;
    $this->strategy = $strategy;
    $this->sendNotifications = ! $disableNotifications;
  }

  public function run(): void {
    $this->backupDestinations->each(function (BackupDestination $backupDestination) {
      try {
        if (! $backupDestination->isReachable()) {
          throw new Exception("Could not connect to disk {$backupDestination->diskName()} because: {$backupDestination->connectionError()}");
        }

        $this->info("Cleaning backups of {$backupDestination->backupName()} on disk {$backupDestination->diskName()}...");

        $this->strategy
          ->setBackupDestination($backupDestination)
          ->deleteOldBackups($backupDestination->backups());

        $this->sendNotification(new CleanupWasSuccessful($backupDestination));

        $usedStorage = Format::humanReadableSize($backupDestination->fresh()->usedStorage());
        consoleOutput()->info("Used storage after cleanup: {$usedStorage}.");
      }
      catch (Exception $exception) {
        consoleOutput()->error("Cleanup failed because: {$exception->getMessage()}.");
        $this->sendNotification(new CleanupHasFailed($exception));

        throw $exception;
      }
    });
  }

  protected function sendNotification($notification): void {
    if ($this->sendNotifications) {
      event($notification);
    }
  }
}
