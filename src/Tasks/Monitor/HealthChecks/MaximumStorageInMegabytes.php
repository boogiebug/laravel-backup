<?php namespace Pinacono\Backup\Tasks\Monitor\HealthChecks;

use Pinacono\Backup\BackupDestination\BackupDestination;
use Pinacono\Backup\Helpers\Format;
use Pinacono\Backup\Tasks\Monitor\HealthCheck;

class MaximumStorageInMegabytes extends HealthCheck {
  protected int $maximumSizeInMegaBytes = 5000;

  public function __construct( int $maximumSizeInMegaBytes = 5000 ) {
    $this->maximumSizeInMegaBytes = $maximumSizeInMegaBytes;
  }

  public function checkHealth(BackupDestination $backupDestination): void {
    $usageInBytes = $backupDestination->usedStorage();

    $this->failIf(
      $this->exceedsAllowance($usageInBytes),
      trans('backup::notifications.unhealthy_backup_found_full', [
        'disk_usage' => $this->humanReadableSize($usageInBytes),
        'disk_limit' => $this->humanReadableSize($this->bytes($this->maximumSizeInMegaBytes)),
      ])
    );
  }

  protected function exceedsAllowance(float $usageInBytes): bool {
    return $usageInBytes > $this->bytes($this->maximumSizeInMegaBytes);
  }

  protected function bytes(int $megaBytes): int {
    return $megaBytes * 1024 * 1024;
  }

  protected function humanReadableSize(float $sizeInBytes): string {
    return Format::humanReadableSize($sizeInBytes);
  }
}