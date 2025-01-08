<?php

// src/MessageHandler/FetchCryptoRatesHandler.php
namespace App\MessageHandler;

use App\Message\FetchCryptoRatesMessage;
use App\Service\CryptoRateFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FetchCryptoRatesHandler implements MessageHandlerInterface
{
private CryptoRateFetcher $fetcher;
private EntityManagerInterface $entityManager;

public function __construct(CryptoRateFetcher $fetcher, EntityManagerInterface $entityManager)
{
$this->fetcher = $fetcher;
$this->entityManager = $entityManager;
}

public function __invoke(FetchCryptoRatesMessage $message)
{
$data = $this->fetcher->fetchRates();

foreach ($data as $item) {
$entity = new \App\Entity\CryptoRate();
$timestamp = new \DateTime($item['last_updated']);
$entity->setCurrencyPair($item['symbol']);
$entity->setRate($item['quote']['USD']['price']);
$entity->setTimestamp($timestamp);
$this->entityManager->persist($entity);
}

$this->entityManager->flush();
}
}
