<?php

declare(strict_types=1);

namespace Scullwm\DbIpClient;

final class ApiThrottling
{
    private const UNKNOWN = 'unknown';

    public function __construct(
        private string $apiKey,
        private int $queriesPerDay,
        private int $queriesLeft,
        private string $status
    ) {
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
            $data['status'] ?? self::UNKNOWN
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
