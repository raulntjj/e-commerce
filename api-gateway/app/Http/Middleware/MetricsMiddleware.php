<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;

class MetricsMiddleware {
    protected $registry;
    
    public function __construct(CollectorRegistry $registry) {
        $this->registry = $registry;
    }
    
    public function handle(Request $request, Closure $next): mixed {
        $start = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $start;
        
        // Coleta métricas
        $this->registry->getOrRegisterCounter(
            'api_gateway',
            'requests_total',
            'Total number of requests',
            ['method', 'service', 'status_code']
        )->inc([
            $request->method(),
            $request->segment(2) ?: 'unknown',
            $response->getStatusCode()
        ]);
        
        $this->registry->getOrRegisterHistogram(
            'api_gateway',
            'request_duration_seconds',
            'Request duration in seconds',
            ['method', 'service'],
            [0.1, 0.5, 1, 2.5, 5, 10]
        )->observe($duration, [
            $request->method(),
            $request->segment(2) ?: 'unknown'
        ]);
        
        return $response;
    }
}