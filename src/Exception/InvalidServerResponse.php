<?php

declare(strict_types=1);

namespace Scullwm\DbIpClient\Exception;

use RuntimeException;

use function sprintf;

final class InvalidServerResponse extends RuntimeException
{
    public static function create(string $query, int $code = 0): self
    {
        return new self(sprintf(
            'The server returned an invalid response (%d) for query "%s".' .
            'We could not parse it.',
            $code,
            $query
        ));
    }

    public static function emptyResponse(string $query): self
    {
        return new self(sprintf('The server returned an empty response for query "%s".', $query));
    }

    public static function invalidJson(string $query, string $body): self
    {
        return new self(sprintf(
            'The server returned a response for query "%s" with invalid JSON "%s".',
            $query,
            $body
        ));
    }
}
