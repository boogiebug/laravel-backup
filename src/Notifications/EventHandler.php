<?php namespace Pinacono\Backup\Notifications;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;

use Pinacono\Backup\Events\BackupHasFailed;
use Pinacono\Backup\Events\BackupWasSuccessful;
use Pinacono\Backup\Events\CleanupHasFailed;
use Pinacono\Backup\Events\CleanupWasSuccessful;
use Pinacono\Backup\Events\HealthyBackupWasFound;
use Pinacono\Backup\Events\UnhealthyBackupWasFound;
use Pinacono\Backup\Exceptions\NotificationCouldNotBeSent;

class EventHandler {
  protected Repository $config;

  function __construct( Repository $config ) {
    $this->config = $config;
  }

  public function subscribe(Dispatcher $events): void {
    $events->listen($this->allBackupEventClasses(), function ($event) {
      $notifiable = $this->determineNotifiable();
      $notification = $this->determineNotification($event);
      $notifiable->notify($notification);
    });
  }

  protected function determineNotifiable() {
    $notifiableClass = $this->config->get('backup.notifications.notifiable');
    return app($notifiableClass);
  }

  protected function determineNotification($event): Notification {
    $lookingForNotificationClass = class_basename($event) . "Notification";

    $notificationClass = collect($this->config->get('backup.notifications.notifications'))
        ->keys()
        ->first(fn (string $notificationClass) => class_basename($notificationClass) === $lookingForNotificationClass);

    if (! $notificationClass) {
      throw NotificationCouldNotBeSent::noNotificationClassForEvent($event);
    }

    return new $notificationClass($event);
  }

  protected function allBackupEventClasses(): array {
    return [
      BackupHasFailed::class,
      BackupWasSuccessful::class,
      CleanupHasFailed::class,
      CleanupWasSuccessful::class,
      HealthyBackupWasFound::class,
      UnhealthyBackupWasFound::class,
    ];
  }
}
