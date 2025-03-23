<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RevolutService
{
  private HttpClientInterface $client;
  private string $apiKey;
  private string $endpoint;

  public function __construct(HttpClientInterface $client, string $apiKey = '', string $endpoint = '')
  {
    $this->client = $client;
    $this->apiKey = $apiKey;
    $this->endpoint = $endpoint;
  }

  public function createPaymentLink(float $amount, string $currency = 'USD'): array
  {
    $response = $this->client->request('POST', "{$this->endpoint}/checkout-link", [
      'headers' => [
        'Authorization' => "Bearer {$this->apiKey}",
        'Content-Type'  => 'application/json',
      ],
      'json' => [
        'amount'      => $amount * 100,
        'currency'    => $currency,
        'description' => 'Paymant',
        'capture_mode' => 'AUTOMATIC',
        'merchant_order_ext_ref' => uniqid(),
        'success_url' => 'https://your-site.com/success',
        'failure_url' => 'https://your-site.com/failure',
      ],
    ]);

    return $response->toArray();
  }
}
