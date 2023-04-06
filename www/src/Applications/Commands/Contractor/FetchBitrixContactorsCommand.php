<?php

declare(strict_types=1);


namespace App\Application\Commands\Contractor;


use App\Application\Commands\FileLockableTrait;
use App\Domain\Contractor\Contractor;
use App\Domain\Contractor\ContractorRepository;
use App\Domain\Contractor\DataFetchers\DataFetcher;
use App\Domain\Contractor\Request\FetchContractorRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchBitrixContactorsCommand extends Command
{
    use FileLockableTrait;

    private $logger;
    private $dataFetcher;
    private $contractorRepository;

    public function __construct(
        DataFetcher $dataFetcher,
        ContractorRepository $contractorRepository
    ) {
        parent::__construct(null);
        $this->dataFetcher = $dataFetcher;
        $this->contractorRepository = $contractorRepository;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('app:contractor:fetch');
        $this->setDescription('Fetch contractors from bitrix.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return Command::SUCCESS;
        }

        $contactorsChangedCount = 0;

        /** @var FetchContractorRequest $fetchContractorRequest */
        foreach ($this->dataFetcher->fetchNewContractors()->iterate() as $fetchContractorRequest) {
            $contactorsChangedCount += $this->contractorRepository->createOrUpdateByExtId(
                new Contractor(
                    null,
                    $fetchContractorRequest->id,
                    $fetchContractorRequest->name,
                    null,
                    $fetchContractorRequest->inn,
                    $fetchContractorRequest->comment,
                    null
                )
            );
        }

        $output->writeln(sprintf('Contractors changed: %d', $contactorsChangedCount));

        $this->release();

        return Command::SUCCESS;
    }

}
