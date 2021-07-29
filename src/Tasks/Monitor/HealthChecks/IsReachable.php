<?php

namespace Pinacono\Backup\Tasks\Monitor\HealthChecks;

use Pinacono\Backup\BackupDestination\BackupDestination;
use Pinacono\Backup\Tasks\Monitor\HealthCheck;

class IsReachable extends HealthCheck
{
    public function checkHealth(BackupDestination $backupDestination): void
    {
        $this->failUnless(
            $backupDestination->isReachable(),
            trans('backup::notifications.unhealthy_backup_found_not_reachable', [
                'error' => $backupDestination->connectionError,
            ])
        );
    }
}
