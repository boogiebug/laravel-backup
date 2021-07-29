<?php

namespace Pinacono\Backup\Tests\Events;

use Illuminate\Support\Facades\Event;
use Pinacono\Backup\Events\CleanupWasSuccessful;
use Pinacono\Backup\Tests\TestCase;

class CleanupWasSuccessfulTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_after_a_cleanup_was_completed_successfully()
    {
        Event::fake();

        $this->artisan('backup:clean');

        Event::assertDispatched(CleanupWasSuccessful::class);
    }
}
