<?php

namespace App\Command;

use App\FootballData\Importer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:import', description: 'Import the Eredivisie teams and matches of the current season')]
class ImportCommand
{
    public function __construct(
        private readonly Importer $importer,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $result = $this->importer->import();

        $io->table(['', 'Created', 'Updated'], [
            ['Teams', $result->teamsCreated, $result->teamsUpdated],
            ['Games', $result->gamesCreated, $result->gamesUpdated],
        ]);
        $io->success('Import finished.');

        return Command::SUCCESS;
    }
}
