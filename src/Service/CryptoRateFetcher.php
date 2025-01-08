<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CryptoRate;

class CryptoRateFetcher
{
    private $logger;
    private $entityManager;
    private $apiKey;


    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager,ParameterBagInterface $params)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->apiKey = $params->get('API_KEY');
    }

    public function fetchRates(string $symbol, string $quote, array $symbols = []): array
    {
        $client = HttpClient::create();
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';

        $query = [
            'start' => 1,
            'limit' => 5,
            'convert' => $quote,
        ];

        if (!empty($symbols)) {
            $query['symbol'] = implode(',', $symbols);
        }
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-CMC_PRO_API_KEY' => $this->apiKey,
                ],
                'query' => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('Error fetching rates');
            }

            $data = $response->toArray()['data'];
            foreach ($data as $item) {

                $entity = new CryptoRate();
                $entity->setCurrencyPair($item['symbol']);
                $entity->setQuoteCurrency($quote);
                if (isset($item['quote'][$quote]['price'])) {
                    $entity->setRate($item['quote'][$quote]['price']);
                }

                $timestamp = new \DateTime($item['last_updated']);
                $entity->setTimestamp($timestamp);
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();
            return $data;
        } catch (\Throwable $e) {
            $this->logger->error('Failed to fetch crypto rates', [
                'error' => $e->getMessage(),
                'query' => $query,
            ]);
            throw $e;
        }
    }
}

