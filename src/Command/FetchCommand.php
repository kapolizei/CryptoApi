<?php

namespace App\Command;

use App\Entity\CryptoCurrency;
use App\Entity\CryptoRate;
use App\Service\CryptoRateFetcher;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Test;
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
    private EntityManagerInterface $entityManager;

    public function __construct(CryptoRateFetcher $cryptoRateFetcher, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->cryptoRateFetcher = $cryptoRateFetcher;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        try {
            $data = $this->cryptoRateFetcher->fetchRates($period);
            foreach ($data as $item) {
                $entity = new CryptoRate();
                $timestamp = new \DateTime($item['last_updated']);
                $entity->setCurrencyPair($item['symbol']);
                $entity->setRate($item['quote']['USD']['price']);
                $entity->setTimestamp($timestamp);
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();
            $output->writeln('<info>Cryptocurrency rates fetched and saved successfully!</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
