<?php

namespace Nacosvel\Authenticator\Contracts;

interface Authentication
{
    /**
     * Generate a random shared secret in Base32 encoding.
     *
     * @param int  $length
     * @param bool $padding
     *
     * @return string
     */
    public static function generateSecret(int $length = 20, bool $padding = false): string;

    /**
     * Generate a token from the given secret and period.
     *
     * @param string $secret
     * @param int    $period
     * @param int    $digits
     * @param string $algo
     *
     * @return string
     */
    public static function generateToken(string $secret, int $period, int $digits = 6, string $algo = 'sha1'): string;

    /**
     * Validate a token against the given secret and time window.
     *
     * @param string $secret
     * @param string $token
     * @param int    $period
     * @param int    $digits
     * @param string $algo
     * @param int    $window
     *
     * @return bool
     */
    public static function validate(string $secret, string $token, int $period, int $digits = 6, string $algo = 'sha1', int $window = 1): bool;
}
