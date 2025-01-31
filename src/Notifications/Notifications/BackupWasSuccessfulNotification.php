<?php namespace Pinacono\Backup\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
//use Illuminate\Notifications\Messages\SlackAttachment;
//use Illuminate\Notifications\Messages\SlackMessage;
use Pinacono\Backup\Events\BackupWasSuccessful;
use Pinacono\Backup\Notifications\BaseNotification;
use Pinacono\Backup\Notifications\Channels\Discord\DiscordMessage;

class BackupWasSuccessfulNotification extends BaseNotification {
  public BackupWasSuccessful $event;

  public function __construct(BackupWasSuccessful $event) {
    $this->event = $event;
  }

  public function toMail(): MailMessage
  {
      $mailMessage = (new MailMessage)
          ->from(config('backup.notifications.mail.from.address', config('mail.from.address')), config('backup.notifications.mail.from.name', config('mail.from.name')))
          ->subject(trans('backup::notifications.backup_successful_subject', ['application_name' => $this->applicationName()]))
          ->line(trans('backup::notifications.backup_successful_body', ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()]));

      $this->backupDestinationProperties()->each(function ($value, $name) use ($mailMessage) {
          $mailMessage->line("{$name}: $value");
      });

      return $mailMessage;
  }

  /*
  public function toSlack(): SlackMessage {
    return (new SlackMessage)
      ->success()
      ->from(config('backup.notifications.slack.username'), config('backup.notifications.slack.icon'))
      ->to(config('backup.notifications.slack.channel'))
      ->content(trans('backup::notifications.backup_successful_subject_title'))
      ->attachment(function (SlackAttachment $attachment) {
        $attachment->fields($this->backupDestinationProperties()->toArray());
      });
  }
  */

  public function toDiscord(): DiscordMessage {
    return (new DiscordMessage())
      ->success()
      ->from(config('backup.notifications.discord.username'), config('backup.notifications.discord.avatar_url'))
      ->title(trans('backup::notifications.backup_successful_subject_title'))
      ->fields($this->backupDestinationProperties()->toArray());
  }
}
