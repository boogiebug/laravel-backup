<?php namespace Pinacono\Backup\Commands;

use Exception;
use Pinacono\Backup\Commands\BaseCommand;
use Pinacono\Backup\Events\BackupHasFailed;
use Pinacono\Backup\Exceptions\InvalidCommand;
use Pinacono\Backup\Tasks\Backup\BackupJobFactory;

class BackupCommand extends BaseCommand {
  protected $signature = 'backup:run
                          {--filename=}
                          {--only-db}
                          {--db-name=*}
                          {--only-files}
                          {--only-to-disk=}
                          {--disable-notifications}
                          {--timeout=}';

  protected $description = 'Run the backup.';

  public function handle() {
    $this->comment('Starting backup...');

    $disableNotifications = $this->option('disable-notifications');

    if ( $this->option('timeout') && is_numeric($this->option('timeout')) ) {
      set_time_limit((int) $this->option('timeout'));
    }

    try {
      $this->guardAgainstInvalidOptions();

      $backupJob = BackupJobFactory::createFromArray(config('backup'));

      if ($this->option('only-db')) {
        $backupJob->dontBackupFilesystem();
      }

      if ($this->option('db-name')) {
        $backupJob->onlyDbName($this->option('db-name'));
      }

      if ($this->option('only-files')) {
        $backupJob->dontBackupDatabases();
      }

      if ($this->option('only-to-disk')) {
        $backupJob->onlyBackupTo($this->option('only-to-disk'));
      }

      if ($this->option('filename')) {
        $backupJob->setFilename($this->option('filename'));
      }

      if ($disableNotifications) {
        $backupJob->disableNotifications();
      }

      /*
      if (! $this->getSubscribedSignals()) {
        $backupJob->disableSignals();
      }
      */

      $backupJob->run();

      $this->comment('Backup completed!');

    }
    catch (Exception $exception) {

      $this->error("Backup failed because: {$exception->getMessage()}.");

      if (! $disableNotifications) {
        event(new BackupHasFailed($exception));
      }

      return 1;
    }
  }

  protected function guardAgainstInvalidOptions() {
    if ( ! $this->option('only-db') ) {
      return;
    }

    if ( ! $this->option('only-files') ) {
      return;
    }

    throw InvalidCommand::create('Cannot use `only-db` and `only-files` together');
  }
}
