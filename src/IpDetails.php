<?php

namespace Scullwm\DbIpClient;

class IpDetails
{
    private string $ipAddress;

    private string $continentCode;

    private string $continentName;

    private string $countryCode;

    private string $countryName;

    private string $isEuMember;

    private string $stateProv;

    private string $city;

    private string $threatLevel;

    private string $isp;

    private function __construct(
        string $ipAddress,
        string $continentCode,
        string $continentName,
        string $countryCode,
        string $countryName,
        bool $isEuMember,
        string $stateProv,
        string $city,
        string $threatLevel,
        string $isp
    ) {
        $this->ipAddress = $ipAddress;
        $this->continentCode = $continentCode;
        $this->continentName = $continentName;
        $this->countryCode = $countryCode;
        $this->countryName = $countryName;
        $this->stateProv = $stateProv;
        $this->isEuMember = $isEuMember;
        $this->city = $city;
        $this->threatLevel = $threatLevel;
        $this->isp = $isp;
    }

    public static function new(array $data): self
    {
        return new self(
            $data['ipAddress'],
            (isset($data['continentCode'])) ? $data['continentCode'] : '',
            (isset($data['continentName'])) ? $data['continentName'] : '',
            (isset($data['countryCode'])) ? $data['countryCode'] : '',
            (isset($data['countryName'])) ? $data['countryName'] : '',
            (isset($data['isEuMember'])) ? $data['isEuMember'] : false,
            (isset($data['stateProv'])) ? $data['stateProv'] : '',
            (isset($data['city'])) ? $data['city'] : '',
            (isset($data['threatLevel'])) ? $data['threatLevel'] : '',
            (isset($data['isp'])) ? $data['isp'] : ''
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

    public function isEuMember(): bool
    {
        return $this->isEuMember;
    }

    public function getStateProv(): string
    {
        return $this->stateProv;
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
}
