<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function PHPUnit\Framework\throwException;

class PaypalService
{
  private string $paypalClientId;
  private string $paypalSecret;
  private string $paypalOrders;
  private string $paypalAccessToken;
  private HttpClientInterface $httpClient;

  public function __construct(
    HttpClientInterface $httpClient,
    string $paypalClientId,
    string $paypalSecret,
    string $paypalAccessToken,
    string $paypalOrders,
  ) {
    $this->httpClient = $httpClient;
    $this->paypalClientId = $paypalClientId;
    $this->paypalSecret = $paypalSecret;
    $this->paypalAccessToken = $paypalAccessToken;
    $this->paypalOrders = $paypalOrders;
  }

  public function createOrder(): JsonResponse
  {
    $accessToken = $this->obtainPaypalAccessToken();

    if (null === $accessToken) {
      throw new \Exception('Something is wrong, please try later.');
    }

    $orderResponse = $this->httpClient->request('POST', $this->paypalOrders, [
      'headers' => [
        'Authorization' => "Bearer $accessToken",
        'Content-Type' => 'application/json',
      ],
      'json' => [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
          'amount' => [
            'currency_code' => 'EUR',
            'value' => '10.00',
          ],
        ]],
      ],
    ]);

    $order = $orderResponse->toArray();

    return new JsonResponse(['id' => $order['id']]);
  }

  public function captureOrder($orderId): JsonResponse
  {
    $accessToken = $this->obtainPaypalAccessToken();

    if (null === $accessToken) {
      throw new \Exception('Something is wrong, please try later.');
    }

    $captureResponse = $this->httpClient->request('POST', "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$orderId}/capture", [
      'headers' => [
        'Authorization' => "Bearer $accessToken",
        'Content-Type' => 'application/json',
        'Prefer' => 'return=representation',
        'PayPal-Request-Id' => 'A v4 style guid',
      ],
    ]);

    return new JsonResponse($captureResponse->toArray());
  }

  public function approveOrder($orderId): JsonResponse
  {
    $accessToken = $this->obtainPaypalAccessToken();

    if (null === $accessToken) {
      throw new \Exception('Something is wrong, please try later.');
    }

    $approveOrder = $this->httpClient->request('POST', "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$orderId}/confirm-payment-source", [
      'headers' => [
        'Authorization' => "Bearer $accessToken",
        'Content-Type' => 'application/json',
      ],
    ]);

    return new JsonResponse($approveOrder->toArray());
  }

  private function obtainPaypalAccessToken(): string
  {
    $accessTokenResponse = $this->httpClient->request('POST', $this->paypalAccessToken, [
      'auth_basic' => [$this->paypalClientId, $this->paypalSecret],
      'body' => ['grant_type' => 'client_credentials'],
    ]);
    $accessToken = $accessTokenResponse->toArray()['access_token'];

    return $accessToken;
  }
}
