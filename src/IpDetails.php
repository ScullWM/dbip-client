<?php

declare(strict_types=1);

namespace Scullwm\DbIpClient;

final class IpDetails
{
    private function __construct(
        private string $ipAddress,
        private string $continentCode,
        private string $continentName,
        private string $countryCode,
        private string $countryName,
        private string $stateProvCode,
        private bool $isEuMember,
        private string $stateProv,
        private string $city,
        private string $threatLevel,
        private bool $isProxy,
        private bool $isCrawler,
        private string $isp
    ) {
    }

    /**
     * @param array<array-key, string|bool> $data
     * @psalm-param array{
     *   ipAddress: string,
     *   ?continentName: string,
     *   ?countryCode: string,
     *   ?countryName: string,
     *   ?stateProvCode: string,
     *   ?isEuMember: bool,
     *   ?stateProv: string,
     *   ?city: string,
     *   ?threatLevel: string,
     *   ?isProxy: string,
     *   ?isCrawler: bool,
     *   ?isp: string
     * } $data
     */
    public static function new(array $data): self
    {
        return new self(
            $data['ipAddress'],
            $data['continentCode'] ?? '',
            $data['continentName'] ?? '',
            $data['countryCode'] ?? '',
            $data['countryName'] ?? '',
            $data['stateProvCode'] ?? '',
            $data['isEuMember'] ?? false,
            $data['stateProv'] ?? '',
            $data['city'] ?? '',
            $data['threatLevel'] ?? '',
            $data['isProxy'] ?? false,
            $data['isCrawler'] ?? false,
            $data['isp'] ?? ''
        );
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getContinentCode(): string
    {
        return $this->continentCode;
    }

    public function getContinentName(): string
    {
        return $this->continentName;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCountryName(): string
    {
        return $this->countryName;
    }

    public function getStateProvCode(): string
    {
        return $this->stateProvCode;
    }

    public function getStateProv(): string
    {
        return $this->stateProv;
    }

    public function isEuMember(): bool
    {
        return $this->isEuMember;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getThreatLevel(): string
    {
        return $this->threatLevel;
    }

    public function getIsp(): string
    {
        return $this->isp;
    }

    public function isRisky(): bool
    {
        return $this->threatLevel !== 'low';
    }

    public function isProxy(): bool
    {
        return $this->isProxy;
    }

    public function isCrawler(): bool
    {
        return $this->isCrawler;
    }
}
