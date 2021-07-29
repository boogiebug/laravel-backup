<?php

namespace Pinacono\Backup\Tasks\Monitor;

use Illuminate\Support\Str;
use Pinacono\Backup\BackupDestination\BackupDestination;
use Pinacono\Backup\Exceptions\InvalidHealthCheck;

abstract class HealthCheck
{
    abstract public function checkHealth(BackupDestination $backupDestination);

    public function name(): string
    {
        return Str::title(class_basename($this));
    }

    protected function fail(string $message): void
    {
        throw InvalidHealthCheck::because($message);
    }

    protected function failIf(bool $condition, string $message): void
    {
        if ($condition) {
            $this->fail($message);
        }
    }

    protected function failUnless(bool $condition, string $message): void
    {
        if (! $condition) {
            $this->fail($message);
        }
    }
}
