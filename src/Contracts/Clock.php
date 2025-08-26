<?php

namespace Nacosvel\Authenticator\Contracts;

interface Clock
{
    public static function now(): int;
}
