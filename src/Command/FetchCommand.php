<?php

namespace App\Command;

use App\Service\CryptoRateFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fetch-crypto-rates',
    description: 'Fetches cryptocurrency rates and saves them into the database.',
)]
class FetchCommand extends Command
{
    private CryptoRateFetcher $cryptoRateFetcher;

    public function __construct(CryptoRateFetcher $cryptoRateFetcher)
    {
        parent::__construct();
        $this->cryptoRateFetcher = $cryptoRateFetcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symbols = ['BTC', 'ETH', 'XRP'];
        $quotes = ['USD', 'EUR'];
        try {
            foreach ($symbols as $symbol) {
                foreach ($quotes as $quote) {
                    $this->cryptoRateFetcher->fetchRates($symbol,$quote);
                }
            }

            $output->writeln('<info>Cryptocurrency rates fetched and saved successfully!</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
