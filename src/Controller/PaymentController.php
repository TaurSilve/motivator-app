<?php

namespace App\Controller;

use App\Service\RevolutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
  private RevolutService $revolutService;

  public function __construct(RevolutService $revolutService)
  {
    $this->revolutService = $revolutService;
  }

  #[Route('/payment/revolut', name: 'payment_revolut', methods: ['POST'])]
  public function createPayment(): JsonResponse
  {
    $payment = $this->revolutService->createPaymentLink(50.0, 'USD');

    return $this->json(['payment_url' => $payment['checkout_url']]);
  }
}
