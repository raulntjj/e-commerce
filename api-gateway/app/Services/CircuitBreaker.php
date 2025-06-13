<?php

namespace App\Services;

class CircuitBreaker {
    protected $redis;
    protected $config;
    
    public function __construct(\Redis $redis) {
        $this->redis = $redis;
        $this->config = [
            'failure_threshold' => config('services.circuit_break.failure_threshold', 3),
            'success_threshold' => config('services.circuit_break.success_threshold', 2),
            'timeout' => config('services.circuit_break.timeout', 60)
        ];
    }
    
    public function isAvailable($serviceName): bool {
        $state = $this->redis->get("circuit:{$serviceName}:state");
        
        if ($state === 'open') {
            $lastChanged = $this->redis->get("circuit:{$serviceName}:last_changed");
            if (time() - $lastChanged < $this->config['timeout']) {
                return false;
            }
            $this->redis->set("circuit:{$serviceName}:state", 'half-open');
        }
        
        return true;
    }
    
    public function reportSuccess($serviceName): void {
        $state = $this->redis->get("circuit:{$serviceName}:state");
        
        if ($state === 'half-open') {
            $successCount = $this->redis->incr("circuit:{$serviceName}:success_count");
            
            if ($successCount >= $this->config['success_threshold']) {
                $this->redis->set("circuit:{$serviceName}:state", 'closed');
                $this->redis->del("circuit:{$serviceName}:failure_count");
                $this->redis->del("circuit:{$serviceName}:success_count");
            }
        }
    }
    
    public function reportFailure($serviceName): void {
        $state = $this->redis->get("circuit:{$serviceName}:state");
        $failureCount = $this->redis->incr("circuit:{$serviceName}:failure_count");
        
        if ($state === 'closed' && $failureCount >= $this->config['failure_threshold']) {
            $this->redis->set("circuit:{$serviceName}:state", 'open');
            $this->redis->set("circuit:{$serviceName}:last_changed", time());
        } elseif ($state === 'half-open') {
            $this->redis->set("circuit:{$serviceName}:state", 'open');
            $this->redis->set("circuit:{$serviceName}:last_changed", time());
        }
    }
}