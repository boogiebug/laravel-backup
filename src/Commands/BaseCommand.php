<?php namespace Pinacono\Backup\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pinacono\Backup\Helpers\ConsoleOutput;

abstract class BaseCommand extends Command {
  public function run(InputInterface $input, OutputInterface $output) {
    app(ConsoleOutput::class)->setCommand($this);
    return parent::run($input, $output);
  }
}