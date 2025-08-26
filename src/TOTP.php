<?php

namespace Nacosvel\Authenticator;

use InvalidArgumentException;
use Nacosvel\Authenticator\Concerns\SystemClock;

class TOTP extends Authentication implements Contracts\Authentication
{
    /**
     * TOTP Generate (RFC 6238)
     *
     * @param string   $secret
     * @param int      $period
     * @param int      $digits
     * @param string   $algo
     * @param int|null $time
     *
     * @return string
     */
    public static function generateToken(string $secret, int $period = 30, int $digits = 6, string $algo = 'sha1', int $time = null): string
    {
        $time = $time ?? SystemClock::now();

        if ($period <= 0) {
            throw new InvalidArgumentException('Invalid period: must be greater than zero.');
        }

        if ($digits < 6 || $digits > 10) {
            throw new InvalidArgumentException('Invalid digits: must be between 6 and 10.');
        }

        try {
            $key = Base32::decode($secret);
            $key = $key ?: $secret;
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        $counter = intdiv($time, $period);
        $binary  = self::packCounterBE($counter);
        $hmac    = self::hmac($algo, $key, $binary);
        $code    = self::dynamicTruncate($hmac, $digits);
        return str_pad($code, $digits, '0', STR_PAD_LEFT);
    }

    /**
     * Dynamic Truncation (RFC 4226 §5.3)
     *
     * @param string $hmac
     * @param int    $digits
     *
     * @return int 31-bit positive integer (DT value)
     */
    protected static function dynamicTruncate(string $hmac, int $digits): int
    {
        $len      = strlen($hmac);
        $offset   = ord($hmac[$len - 1]) & 0x0F;
        $unpacked = unpack('N', substr($hmac, $offset, 4));
        $binCode  = $unpacked[1] & 0x7FFFFFFF;
        return bcmod($binCode, 10 ** $digits);
    }

    /**
     * Validate a time-based one-time password (TOTP) token against a shared secret.
     *
     * @param string   $secret
     * @param string   $token
     * @param int      $period
     * @param int      $digits
     * @param string   $algo
     * @param int      $window
     * @param int|null $time
     *
     * @return bool
     */
    public static function validate(string $secret, string $token, int $period = 30, int $digits = 6, string $algo = 'sha1', int $window = 1, int $time = null): bool
    {
        $time = $time ?? SystemClock::now();

        for ($i = -$window; $i <= $window; $i++) {
            $candidate = self::generateToken($secret, $period, $digits, $algo, $time + $i * $period);
            if (hash_equals($candidate, self::normalizeToken($token, $digits))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a TOTP authentication URI for a given account and secret.
     *
     * This method creates a URI instance of type 'totp' with the specified account,
     *  optional issuer, secret, and TOTP parameters including period, number of digits,
     *  and hashing algorithm. The resulting URI can be used with authenticator apps.
     *
     * @param string      $secret  The shared secret for TOTP generation (Base32-encoded).
     * @param string      $account The account name or identifier.
     * @param string|null $issuer  Optional issuer or service provider name.
     * @param int         $period  The time step in seconds (default: 30).
     * @param int         $digits  The number of digits in the generated token (default: 6).
     * @param string      $algo    The hashing algorithm (default: 'sha1').
     *
     * @return URI
     */
    public static function getAuthUri(string $secret, string $account, string $issuer = null, int $period = 30, int $digits = 6, string $algo = 'sha1'): URI
    {
        return self::generateURI('totp', $account, $issuer)->push([
            'secret'    => $secret,
            'period'    => $period,
            'digits'    => $digits,
            'algorithm' => $algo,
        ]);
    }
}
