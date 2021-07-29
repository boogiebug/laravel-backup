<?php namespace Pinacono\Backup;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use Pinacono\Backup\Events\BackupZipWasCreated;
use Pinacono\Backup\Helpers\ConsoleOutput;
use Pinacono\Backup\Listeners\EncryptBackupArchive;
use Pinacono\Backup\Notifications\Channels\Discord\DiscordChannel;
use Pinacono\Backup\Notifications\EventHandler;
use Pinacono\Backup\Tasks\Cleanup\CleanupStrategy;

class ServiceProvider extends BaseServiceProvider {

  protected $console_commands = [
    \Pinacono\Backup\Commands\BackupCommand::class,
    \Pinacono\Backup\Commands\CleanupCommand::class,
    \Pinacono\Backup\Commands\ListCommand::class,
    \Pinacono\Backup\Commands\MonitorCommand::class
  ];

  /**
   * Register the application services.
   *
   * @return void
   */
  public function register() {
    $this->app['events']->subscribe(EventHandler::class);
    $this->app->singleton(ConsoleOutput::class);
    $this->app->bind(CleanupStrategy::class, config('backup.cleanup.strategy'));
    //$this->registerDiscordChannel();
  }

  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot(\Illuminate\Routing\Router $router, \Illuminate\Contracts\Http\Kernel $kernel) {

    // Setup the default configuration.
    $this->mergeConfigFrom(__DIR__.'/../config/backup.php', 'backup');

    // console mode
    if ( $this->app->runningInConsole() ) {
      // Setup the resource publishing groups.
      $this->publishes([
        __DIR__.'/../config/backup.php' =>
          config_path('backup.php')
      ], 'laravel-backup');

      // Register the Artisan commands.
      $this->commands($this->console_commands);
    }

    if (EncryptBackupArchive::shouldEncrypt()) {
      Event::listen(BackupZipWasCreated::class, EncryptBackupArchive::class);
    }
  }

  protected function registerDiscordChannel() {
    Notification::resolved(function (ChannelManager $service) {
      $service->extend('discord', function ($app) {
        return new DiscordChannel();
      });
    });
  }
}
