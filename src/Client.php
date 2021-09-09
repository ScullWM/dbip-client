<?php

declare(strict_types=1);

namespace Scullwm\DbIpClient;

use Http\Client\HttpClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Scullwm\DbIpClient\Exception\InvalidCredentials;
use Scullwm\DbIpClient\Exception\InvalidServerResponse;
use Scullwm\DbIpClient\Exception\QuotaExceeded;

use function json_decode;
use function json_last_error;
use function sprintf;

use const JSON_ERROR_NONE;

class Client
{
    private ClientInterface $client;

    private RequestFactoryInterface $messageFactory;

    private string $token;

    private const API_ENDPOINT_V2_IP_DETAILS = 'http://api.db-ip.com/v2/%s/%s';
    private const API_ENDPOINT_V2_API_STATUS = 'http://api.db-ip.com/v2/%s';

    public function __construct(
        string $token,
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $factory = null
    ) {
        $this->token          = $token;
        $this->client         = $client ?: Psr18ClientDiscovery::find();
        $this->messageFactory = $factory ?: Psr17FactoryDiscovery::findRequestFactory();
    }

    public function getIpDetails(string $ip): IpDetails
    {
        $request = $this->getRequest(sprintf(self::API_ENDPOINT_V2_IP_DETAILS, $this->token, $ip));

        return IpDetails::new($this->getParsedResponse($request));
    }

    public function getApiStatus(): ApiThrottling
    {
        $request = $this->getRequest(sprintf(self::API_ENDPOINT_V2_API_STATUS, $this->token));

        return ApiThrottling::new($this->getParsedResponse($request), $this->token);
    }

    /**
     * @psalm-return array<string, string>
     *
     * @todo can we get the right array shape here?
     */
    protected function getParsedResponse(RequestInterface $request): array
    {
        $response = $this->getHttpClient()->sendRequest($request);

        $statusCode = $response->getStatusCode();
        if ($statusCode === 401 || $statusCode === 403) {
            throw new InvalidCredentials();
        }

        if ($statusCode === 429) {
            throw new QuotaExceeded();
        }

        if ($statusCode >= 300) {
            throw InvalidServerResponse::create((string) $request->getUri(), $statusCode);
        }

        $body = (string) $response->getBody();
        if ($body === '') {
            throw InvalidServerResponse::emptyResponse((string) $request->getUri());
        }

        $content = json_decode($body, true);

        if ($content && json_last_error() !== JSON_ERROR_NONE) {
            throw InvalidServerResponse::invalidJson((string) $request->getUri(), $body);
        }

        return $content;
    }

    protected function getRequest(string $url): RequestInterface
    {
        return $this->getMessageFactory()->createRequest('GET', $url);
    }

    protected function getHttpClient(): HttpClient
    {
        return $this->client;
    }

    protected function getMessageFactory(): RequestFactoryInterface
    {
        return $this->messageFactory;
    }
}
