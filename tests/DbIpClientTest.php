<?php

declare(strict_types=1);

namespace Scullwm\DbIpClient\Tests;

use Scullwm\DbIpClient\Client as TestedClient;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

final class DbIpClientTest extends TestCase
{
    /**
     * @dataProvider provideIpListing
     */
    public function testGettingIp(string $ip): void
    {
        $client = new Client();

        $response = $this->getResponse(file_get_contents(__DIR__ . sprintf('/Fixtures/%s.json', $ip)));
        $client->setDefaultResponse($response);
        $dbipClient = new TestedClient('faketoken', $client);

        $ipDetails = $dbipClient->getIpDetails($ip);

        self::assertEquals($ipDetails->getIpAddress(), $ip);
    }

    public function provideIpListing()
    {
        return [
            ['8.8.8.8'],
            ['12.25.8.200'],
            ['192.168.1.1'],
            ['2.2.2.2'],
        ];
    }

    public function testFullDetail(): void
    {
        $client = new Client();

        $response = $this->getResponse(file_get_contents(__DIR__ . '/Fixtures/12.25.8.200.json'));
        $client->setDefaultResponse($response);
        $dbipClient = new TestedClient('faketoken', $client);

        $ipDetails = $dbipClient->getIpDetails('12.25.8.200');

        self::assertFalse($ipDetails->isRisky());
        self::assertFalse($ipDetails->isEuMember());

        self::assertEquals('NA', $ipDetails->getContinentCode());
        self::assertEquals('North America', $ipDetails->getContinentName());
        self::assertEquals('US', $ipDetails->getCountryCode());
        self::assertEquals('États-Unis', $ipDetails->getCountryName());
        self::assertEquals('Caroline du Sud', $ipDetails->getStateProv());
        self::assertEquals('North Charleston', $ipDetails->getCity());
        self::assertEquals('low', $ipDetails->getThreatLevel());
        self::assertEquals('AT&T Services', $ipDetails->getIsp());
        self::assertEquals(false, $ipDetails->isCrawler());
    }

    public function testApiStatus(): void
    {
        $client = new Client();

        $response = $this->getResponse(file_get_contents(__DIR__ . '/Fixtures/status.json'));
        $client->setDefaultResponse($response);
        $hash = 'd74be40a1acd2b5b356f67a0f6a5e1be';

        $dbipClient = new TestedClient($hash, $client);

        $apiStatus = $dbipClient->getApiStatus();

        self::assertEquals($hash, $apiStatus->getApiKey());
        self::assertEquals(10000, $apiStatus->getQueriesPerDay());
        self::assertEquals(9986, $apiStatus->getQueriesLeft());
        self::assertEquals('active', $apiStatus->getStatus());
    }

    public function testFreeApiStatus(): void
    {
        $client = new Client();

        $response = $this->getResponse(file_get_contents(__DIR__ . '/Fixtures/status_free.json'));
        $client->setDefaultResponse($response);
        $dbipClient = new TestedClient('free', $client);

        $apiStatus = $dbipClient->getApiStatus();

        self::assertEquals('free', $apiStatus->getApiKey());
        self::assertEquals(0, $apiStatus->getQueriesPerDay());
        self::assertEquals(969, $apiStatus->getQueriesLeft());
        self::assertEquals('unknown', $apiStatus->getStatus());
    }

    private function getResponse(string $content, int $statusCode = 200): ResponseInterface
    {
        $body = $this->createMock(StreamInterface::class);
        $body
            ->expects(self::once())
            ->method('__toString')
            ->willReturn($content);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method('getBody')
            ->willReturn($body);

        $response
            ->expects(self::once())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        return $response;
    }
}
