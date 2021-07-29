<?php

namespace Pinacono\Backup\Tests\Commands;

use Pinacono\Backup\Tests\TestCase;

class ListCommandTest extends TestCase
{
    /** @test */
    public function it_can_run_the_list_command()
    {
        config()->set('backup.backup.destination.disks', [
            'local',
        ]);

        $this->artisan('backup:list')->assertExitCode(0);
    }
}
