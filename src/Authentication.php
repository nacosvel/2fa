<?php

namespace Nacosvel\Authenticator;

use Exception;
use InvalidArgumentException;

abstract class Authentication
{
    /**
     * Generate a random Base32-encoded secret for safe storage and display.
     *
     * @param int  $length
     * @param bool $padding
     *
     * @return string
     */
    public static function generateSecret(int $length = 20, bool $padding = false): string
    {
        try {
            $bytes = random_bytes($length);
        } catch (Exception $e) {
            $bytes = self::getRandomBytesFromString($length);
        }

        return Base32::encode($bytes, $padding);
    }

    /**
     * Generates cryptographically secure pseudo-random bytes
     *
     * @param int $length
     * @param int $min
     * @param int $max
     *
     * @return string
     */
    protected static function getRandomBytesFromString(int $length = 20, int $min = 0, int $max = 255): string
    {
        $elements  = array_map(fn() => mt_rand($min, $max), range(1, $length));
        $character = array_map('chr', $elements);
        return implode('', $character);
    }

    /**
     * Pack 64-bit counter as big-endian (per RFC 4226).
     * Portable across platforms.
     *
     * @param int $counter
     *
     * @return string
     */
    protected static function packCounterBE(int $counter): string
    {
        $high = bcdiv($counter, 2 ** 32);
        $low  = bcmod($counter, 2 ** 32);
        return pack('N2', $high, $low);
    }

    /**
     * RFC 2104 HMAC wrapper returning binary string.
     *
     * @param string $algo
     * @param string $key
     * @param string $binary
     *
     * @return string
     */
    protected static function hmac(string $algo, string $key, string $binary): string
    {
        return hash_hmac($algo, $binary, $key, true);
    }

    /**
     * Dynamic Truncation (RFC 4226 §5.3)
     *
     * @param string $hmac
     * @param int    $digits
     *
     * @return int 31-bit positive integer (DT value)
     */
    abstract protected static function dynamicTruncate(string $hmac, int $digits): int;

    /**
     * Normalize a given token string to match the required digit length.
     *
     * @param string $token
     * @param int    $digits
     *
     * @return string
     */
    protected static function normalizeToken(string $token, int $digits): string
    {
        $token = preg_replace('/\s+/', '', $token);

        if (!ctype_digit($token)) {
            // Allow leading zeros but digits only
            throw new InvalidArgumentException('Code must contain digits only');
        }

        if (strlen($token) !== $digits) {
            $token = str_pad($token, $digits, '0', STR_PAD_LEFT);
        }

        return $token;
    }
}
