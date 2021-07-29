<?php namespace Pinacono\Backup\Commands;

use Pinacono\Backup\Commands\BaseCommand;
use Pinacono\Backup\Events\HealthyBackupWasFound;
use Pinacono\Backup\Events\UnhealthyBackupWasFound;
use Pinacono\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class MonitorCommand extends BaseCommand {
  /** @var string */
  protected $signature = 'backup:monitor';

  /** @var string */
  protected $description = 'Monitor the health of all backups.';

  public function handle() {
    $hasError = false;

    $statuses = BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitor_backups'));

    foreach ($statuses as $backupDestinationStatus) {
      $diskName = $backupDestinationStatus->backupDestination()->diskName();

      if ( $backupDestinationStatus->isHealthy() ) {
        $this->info("The backups on {$diskName} are considered healthy.");
        event(new HealthyBackupWasFound($backupDestinationStatus));
      }
      else {
        $hasError = true;
        $this->error("The backups on {$diskName} are considered unhealthy!");
        event(new UnhealthyBackupWasFound($backupDestinationStatus));
      }
    }

    if ( $hasError ) {
      return 1;
    }
  }
}
