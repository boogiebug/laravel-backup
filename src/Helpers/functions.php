<?php use Pinacono\Backup\Helpers\ConsoleOutput;

function consoleOutput(): ConsoleOutput {
  return app(ConsoleOutput::class);
}
