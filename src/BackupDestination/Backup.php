<?php namespace Pinacono\Backup\BackupDestination;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use InvalidArgumentException;

use Pinacono\Backup\Exceptions\InvalidBackupFile;
use Pinacono\Backup\Tasks\Backup\BackupJob;

class Backup {
  protected bool $exists = true;
  protected ?Carbon $date = null;
  protected ?int $size = null;
  protected Filesystem $disk;
  protected string $path;

  public function __construct( Filesystem $disk, string $path ) {
    $this->disk = $disk;
    $this->path = $path;
  }

  public function disk(): Filesystem {
    return $this->disk;
  }

  public function path(): string {
    return $this->path;
  }

  public function exists(): bool {
    if ($this->exists === null) {
      $this->exists = $this->disk->exists($this->path);
    }

    return $this->exists;
  }

  public function date(): Carbon {
    if ($this->date === null) {
      try {
        $basename = basename($this->path);

        $this->date = Carbon::createFromFormat(BackupJob::FILENAME_FORMAT, $basename);
      }
      catch (InvalidArgumentException $e) {
        $this->date = Carbon::createFromTimestamp($this->disk->lastModified($this->path));
      }
    }

    return $this->date;
  }

  public function sizeInBytes(): float {
    if ($this->size === null) {
      if (! $this->exists()) {
        return 0;
      }

      $this->size = $this->disk->size($this->path);
    }

    return $this->size;
  }

  public function stream() {
    return throw_unless(
      $this->disk->readStream($this->path),
      InvalidBackupFile::readError($this)
    );
  }

  public function delete(): void {
    if (! $this->disk->delete($this->path)) {
      $this->error("Failed to delete backup `{$this->path}`.");

      return;
    }

    $this->exists = false;

    $this->info("Deleted backup `{$this->path}`.");
  }
}
