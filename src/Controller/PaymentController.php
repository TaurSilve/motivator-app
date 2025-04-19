<?php

namespace App\Controller;

use App\Service\PaypalService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * class PaymentController controll payment methods.
 */
class PaymentController extends AbstractController
{
  private PaypalService $paypalService;

  public function __construct(PaypalService $paypalService)
  {
    $this->paypalService = $paypalService;
  }

  #[Route('/CreateOrder', name: 'payment_paypal_create_order', methods: ['POST'])]
  public function createPayment(): JsonResponse
  {
    try {
      $payment = $this->paypalService->createOrder();
    } catch (\Exception $e) {
      throw new Exception($e->getMessage());
    }

    return $payment;
  }

  #[Route('/CaptureOrder/{orderId}', name: 'payment_paypal_capture_order', methods: ['POST'])]
  public function captureOrder(string $orderId): JsonResponse
  {
    if (null === $orderId) {
      throw new \Exception('Please provide valid order Id!');
    }

    try {
      $payment = $this->paypalService->captureOrder($orderId);
    } catch (\Exception $e) {
      throw new Exception($e->getMessage());
    }

    return $payment;
  }
}
