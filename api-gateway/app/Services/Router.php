<?php

namespace App\Services;

use App\Handlers\ServiceUnavailableHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Services\CircuitBreaker;

class Router {
    protected $client;
    protected $circuitBreaker;

    public function __construct(Client $client, CircuitBreaker $circuitBreaker) {
        $this->client = $client;
        $this->circuitBreaker = $circuitBreaker;
    }

    public function route(Request $request, $serviceName, $path){
        if (!$this->circuitBreaker->isAvailable($serviceName)) {
            return ServiceUnavailableHandler::handle(
                new RequestException('Circuit Breaker triggered', $request)
            );
        }

        try {
            $serviceConfig = config("services.services.{$serviceName}");
            
            if (!$serviceConfig) {
                return response()->json(['error' => 'Service not found'], 404);
            }

            $response = $this->client->request(
                $request->method(),
                $serviceConfig['base_uri'] . $path,
                [
                    'headers' => $this->prepareHeaders($request),
                    'query' => $request->query(),
                    'json' => $request->json()->all(),
                    'timeout' => $serviceConfig['timeout']
                ]
            );
            
            $this->circuitBreaker->reportSuccess($serviceName);
            
            return response(
                $response->getBody()->getContents(),
                $response->getStatusCode(),
                $response->getHeaders()
            );
        } catch (RequestException $e) {
            $this->circuitBreaker->reportFailure($serviceName);
            return ServiceUnavailableHandler::handle($e);
        }
    }

    protected function prepareHeaders(Request $request): array {
        $headers = [];
    
        foreach ($request->headers as $key => $values) {
            if (in_array(strtolower($key), ['authorization', 'content-type', 'accept', 'x-request-id'])) {
                $headers[$key] = $values;
            }
        }
    
        $headers['X-Request-ID'] = $request->header('X-Request-ID', uniqid());
        return $headers;
    }
}