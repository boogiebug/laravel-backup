<?php

namespace Pinacono\Backup\Tests\Notifications;

use Exception;
use Illuminate\Support\Facades\Notification;
use Pinacono\Backup\BackupDestination\BackupDestinationFactory;
use Pinacono\Backup\Events\BackupHasFailed;
use Pinacono\Backup\Notifications\Notifiable;
use Pinacono\Backup\Notifications\Notifications\BackupHasFailedNotification as BackupHasFailedNotification;
use Pinacono\Backup\Tests\TestCase;

class EventHandlerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    /** @test */
    public function it_will_send_a_notification_by_default_when_a_backup_has_failed()
    {
        $this->fireBackupHasFailedEvent();

        Notification::assertSentTo(new Notifiable(), BackupHasFailedNotification::class);
    }

    /**
     * @test
     *
     * @dataProvider channelProvider
     *
     * @param array $expectedChannels
     */
    public function it_will_send_a_notification_via_the_configured_notification_channels(array $expectedChannels)
    {
        config()->set('backup.notifications.notifications.'.BackupHasFailedNotification::class, $expectedChannels);

        $this->fireBackupHasFailedEvent();

        Notification::assertSentTo(new Notifiable(), BackupHasFailedNotification::class, function ($notification, $usedChannels) use ($expectedChannels) {
            return $expectedChannels == $usedChannels;
        });
    }

    public function channelProvider()
    {
        return [
            [[]],
            [['mail']],
            [['mail', 'slack']],
            [['mail', 'slack', 'discord']],
        ];
    }

    protected function fireBackupHasFailedEvent()
    {
        $exception = new Exception('Dummy exception');

        $backupDestination = BackupDestinationFactory::createFromArray(config('backup.backup'))->first();

        event(new BackupHasFailed($exception, $backupDestination));
    }
}
