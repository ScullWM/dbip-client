<?php

declare(strict_types=1);

namespace Scullwm\DbIpClient;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Scullwm\DbIpClient\Exception\InvalidCredentials;
use Scullwm\DbIpClient\Exception\InvalidServerResponse;
use Scullwm\DbIpClient\Exception\QuotaExceeded;
use Throwable;
use Webmozart\Assert\Assert;

use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class Client
{
    private ClientInterface $client;

    private RequestFactoryInterface $messageFactory;

    private string $token;

    private const API_ENDPOINT_V2_IP_DETAILS = 'https://api.db-ip.com/v2/%s/%s';
    private const API_ENDPOINT_V2_API_STATUS = 'https://api.db-ip.com/v2/%s';

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
     * @throws ClientExceptionInterface
     *
     * @todo can we get the right array shape here?
     */
    protected function getParsedResponse(RequestInterface $request): array
    {
        $response = $this->getHttpClient()->sendRequest($request);

        $statusCode = match ($statusCode = $response->getStatusCode()) {
            401, 403 => throw new InvalidCredentials(),
            429 => throw new QuotaExceeded(),
            default => $statusCode,
        };

        if ($statusCode >= 300) {
            throw InvalidServerResponse::create((string) $request->getUri(), $statusCode);
        }

        $body = (string) $response->getBody();

        Assert::stringNotEmpty(
            $body,
            sprintf('The server returned an empty response for query "%s".', $request->getUri())
        );

        try {
            $content = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw InvalidServerResponse::invalidJson((string) $request->getUri(), $body);
        }

        return $content;
    }

    protected function getRequest(string $url): RequestInterface
    {
        return $this->getMessageFactory()->createRequest('GET', $url);
    }

    protected function getHttpClient(): ClientInterface
    {
        return $this->client;
    }

    protected function getMessageFactory(): RequestFactoryInterface
    {
        return $this->messageFactory;
    }
}
