<?php

namespace Pinacono\Backup\Tests\Events;

use Illuminate\Support\Facades\Event;
use Pinacono\Backup\Events\DumpingDatabase;
use Pinacono\Backup\Tests\TestCase;

class DumpingDatabaseTest extends TestCase
{
    /** @test */
    public function it_will_fire_a_dumping_database_event()
    {
        Event::fake();

        $this->artisan('backup:run');

        Event::assertDispatched(DumpingDatabase::class);
    }
}
