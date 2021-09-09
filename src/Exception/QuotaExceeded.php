<?php

declare(strict_types=1);

namespace Scullwm\DbIpClient\Exception;

use RuntimeException;

final class QuotaExceeded extends RuntimeException implements DbIpError
{
}
