<?php

namespace App\Http;

class HttpRequest
{
  private string $baseUrl;
  private array $defaultHeaders;
  private int $timeout;

  public function __construct(string $baseUrl = '', array $defaultHeaders = [], int $timeout = 30)
  {
    $this->baseUrl = $baseUrl;
    $this->defaultHeaders = $defaultHeaders;
    $this->timeout = $timeout;
  }

  private function sendRequest(string $method, string $url, array $headers = [], array $body = []): array
  {
    $curl = curl_init();
    $fullUrl = $this->baseUrl . $url;

    $options = [
      CURLOPT_URL => $fullUrl,
      CURLOPT_CUSTOMREQUEST => strtoupper($method),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => $this->timeout,
      CURLOPT_HTTPHEADER => $this->buildHeaders($headers),
    ];

    if (!empty($body)) {
      $options[CURLOPT_POSTFIELDS] = json_encode($body);
    }

    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);

    curl_close($curl);

    if ($error) {
      throw new \Exception("CURL Error: $error");
    }

    return [
      'status' => $httpCode,
      'body' => json_decode($response, true),
    ];
  }

  private function buildHeaders(array $headers): array
  {
    $mergedHeaders = array_merge($this->defaultHeaders, $headers);
    return array_map(fn($key, $value) => "$key: $value", array_keys($mergedHeaders), $mergedHeaders);
  }

  public function get(string $url, array $headers = []): array
  {
    return $this->sendRequest('GET', $url, $headers);
  }

  public function post(string $url, array $headers = [], array $body = []): array
  {
    return $this->sendRequest('POST', $url, $headers, $body);
  }

  // Outros métodos HTTP como PUT, DELETE, PATCH podem ser adicionados aqui.
}
