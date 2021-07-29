<?php namespace Pinacono\Backup\Tasks\Monitor;

use Exception;
use Pinacono\Backup\Exceptions\InvalidHealthCheck;

class HealthCheckFailure {
  protected HealthCheck $healthCheck;
  protected Exception $exception;

  public function __construct( HealthCheck $healthCheck, Exception $exception ) {
    $this->healthCheck = $healthCheck;
    $this->exception   = $exception;
  }

  public function healthCheck(): HealthCheck {
    return $this->healthCheck;
  }

  public function exception(): Exception {
    return $this->exception;
  }

  public function wasUnexpected(): bool {
    return ! $this->exception instanceof InvalidHealthCheck;
  }
}
