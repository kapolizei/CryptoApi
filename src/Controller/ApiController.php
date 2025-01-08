<?php
// src/Controller/ApiController.php
namespace App\Controller;

use App\Command\FetchCommand;
use App\Entity\CryptoRate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CryptoCurrency;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CryptoRateFetcher;

class ApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FetchCommand $fetchCommand;

    public function __construct(EntityManagerInterface $entityManager, FetchCommand $fetchCommand, CryptoRateFetcher $cryptoRateFetcher)
    {
        $this->entityManager = $entityManager;
        $this->fetchCommand = $fetchCommand;
        $this->cryptoRateFetcher = $cryptoRateFetcher;
    }

    #[Route('/fetch/{symbol}/{quote}', name: 'fetch_and_save')]
    public function FetchAndSave(string $symbol = 'all', string $quote = 'USD'): Response
    {
        if ($symbol !== 'all') {
            $data = $this->cryptoRateFetcher->fetchRates($symbol, $quote);
            $cryptoRate = $this->entityManager->getRepository(CryptoRate::class)->findOneBy(['currencyPair' => $symbol, 'quoteCurrency' => $quote], ['id' => 'DESC']);

            $responseData = [
                $symbol => $cryptoRate ? $cryptoRate->exportToArray() : null,
            ];
        } else {
            $responseData = $this->fetchAllRates($quote);
        }
        return new JsonResponse($responseData);
    }

    private function fetchAllRates(string $quote = 'USD'): array
    {
        $symbols = ['BTC', 'ETH', 'XRP'];
        $responseData = [];

        foreach ($symbols as $symbol) {
            $data = $this->cryptoRateFetcher->fetchRates($symbol, $quote); $cryptoRate = $this->entityManager->getRepository(CryptoRate::class)->findOneBy(['currencyPair' => $symbol, 'quoteCurrency' => $quote], ['id' => 'DESC']);
            $responseData[$symbol] = $cryptoRate ? $cryptoRate->exportToArray() : null;
        }
        return $responseData;
    }
}
