<?php

namespace Nacosvel\Authenticator\Concerns;

use Nacosvel\Authenticator\Contracts\Clock;

final class SystemClock implements Clock
{
    public static function now(): int
    {
        return time();
    }
}
