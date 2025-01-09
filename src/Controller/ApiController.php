<?php
// src/Controller/ApiController.php
namespace App\Controller;

use App\Entity\CryptoRate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/fetch/{symbol}/{quote}', name: 'fetch_and_save_default')]
    #[Route('/fetch/{symbol}/{quote}/{date}', name: 'fetch_and_save')]
    public function fetchAndSave(string $symbol = 'all', string $quote = 'USD', string $date = null): Response
    {
        if ($symbol !== 'all') {
            if (!$date) {
                $cryptoRate = $this->entityManager->getRepository(CryptoRate::class)->findOneBy(['currencyPair' => $symbol, 'quoteCurrency' => $quote], ['id' => 'DESC']);
            } else {
                $cryptoRate = $this->entityManager->getRepository(CryptoRate::class)->findCurrencyPairRateByDate($symbol, $date);
            }
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
            $cryptoRate = $this->entityManager->getRepository(CryptoRate::class)->findOneBy(['currencyPair' => $symbol, 'quoteCurrency' => $quote], ['id' => 'DESC']);
            $responseData[$symbol] = $cryptoRate ? $cryptoRate->exportToArray() : null;
        }
        return $responseData;
    }
}
