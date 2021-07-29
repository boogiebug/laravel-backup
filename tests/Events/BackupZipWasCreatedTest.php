<?php

namespace Pinacono\Backup\Tests\Events;

use Illuminate\Support\Facades\Event;
use Pinacono\Backup\Events\BackupZipWasCreated;
use Pinacono\Backup\Tests\TestCase;

class BackupZipWasCreatedTest extends TestCase
{
    /** @test */
    public function it_will_fire_a_backup_zip_was_created_event_when_the_zip_was_created()
    {
        Event::fake();

        $this->artisan('backup:run', ['--only-files' => true]);

        Event::assertDispatched(BackupZipWasCreated::class);
    }
}
