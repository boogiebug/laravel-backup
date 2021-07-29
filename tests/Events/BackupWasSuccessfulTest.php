<?php

namespace Pinacono\Backup\Tests\Events;

use Illuminate\Support\Facades\Event;
use Pinacono\Backup\Events\BackupWasSuccessful;
use Pinacono\Backup\Tests\TestCase;

class BackupWasSuccessfulTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_after_a_backup_was_completed_successfully()
    {
        Event::fake();

        $this->artisan('backup:run', ['--only-files' => true]);

        Event::assertDispatched(BackupWasSuccessful::class);
    }
}
