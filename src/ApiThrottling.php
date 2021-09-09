<?php

declare(strict_types=1);

namespace Scullwm\DbIpClient;

class ApiThrottling
{
    private string $apiKey;
    private int $queriesPerDay;
    private int $queriesLeft;
    private string $status;

    public function __construct(string $apiKey, int $queriesPerDay, int $queriesLeft, string $status)
    {
        $this->apiKey        = $apiKey;
        $this->queriesPerDay = $queriesPerDay;
        $this->queriesLeft   = $queriesLeft;
        $this->status        = $status;
    }

    /**
     * @param (string|int)[] $data
     * @psalm-param array{apiKey: 'string', queriesPerDay: int, queriesLeft: int, status: string} $data
     */
    public static function new(array $data, string $defaultApiToken = ''): self
    {
        return new self(
            $data['apiKey'] ?? $defaultApiToken,
            $data['queriesPerDay'] ?? 0,
            $data['queriesLeft'] ?? 0,
            $data['status'] ?? 'unknown'
        );
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getQueriesPerDay(): int
    {
        return $this->queriesPerDay;
    }

    public function getQueriesLeft(): int
    {
        return $this->queriesLeft;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
