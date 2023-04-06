<?php

declare(strict_types=1);


namespace App\Application\Commands\Task;


use App\Application\Commands\FileLockableTrait;
use App\Domain\Contractor\ContractorRepository;
use App\Domain\Task\DataSender\DataSender;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendBitrixTasksDistanceCommand extends Command
{
  use FileLockableTrait;

  private $logger;
  private $dataSender;

  public function __construct(
    DataSender $dataSender
  ) {
    parent::__construct(null);
    $this->dataSender = $dataSender;
  }

  protected function configure()
  {
    parent::configure();

    $this->setName('app:task:send');
    $this->setDescription('Send task distance to bitrix.');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    if (!$this->lock()) {
      $output->writeln('The command is already running in another process.');

      return Command::SUCCESS;
    }

    $this->dataSender->sendTaskDistance();

    $output->writeln(sprintf('Contractors changed: %d', 1));

    $this->release();

    return Command::SUCCESS;
  }

}
