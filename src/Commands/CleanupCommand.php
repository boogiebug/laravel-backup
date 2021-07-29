<?php namespace Pinacono\Backup\Commands;

use Exception;
use Illuminate\Console\Command as BaseCommand;

use Pinacono\Backup\BackupDestination\BackupDestinationFactory;
use Pinacono\Backup\Events\CleanupHasFailed;
use Pinacono\Backup\Tasks\Cleanup\CleanupJob;
use Pinacono\Backup\Tasks\Cleanup\CleanupStrategy;

class CleanupCommand extends BaseCommand {

  /** @var string */
  protected $signature = 'backup:clean {--disable-notifications}';

  /** @var string */
  protected $description = 'Remove all backups older than specified number of days in config.';

  protected CleanupStrategy $strategy;

  public function __construct(CleanupStrategy $strategy) {
    parent::__construct();
    $this->strategy = $strategy;
  }

  public function handle() {
    $this->comment('Starting cleanup...');

    $disableNotifications = $this->option('disable-notifications');

    try {
      $config = config('backup');
      $backupDestinations = BackupDestinationFactory::createFromArray($config['backup']);
      $cleanupJob = new CleanupJob($backupDestinations, $this->strategy, $disableNotifications);
      $cleanupJob->run();
      $this->comment('Cleanup completed!');

    }
    catch (Exception $exception) {
      if ( ! $disableNotifications ) {
        event(new CleanupHasFailed($exception));
      }

      return 1;
    }
  }
}
