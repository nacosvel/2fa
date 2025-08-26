<?php

namespace Nacosvel\Authenticator;

use InvalidArgumentException;

class HOTP extends Authentication implements Contracts\Authentication
{
    /**
     * HOTP Generate (RFC 4226)
     *
     * @param string $secret
     * @param int    $period
     * @param int    $digits
     * @param string $algo
     *
     * @return string
     */
    public static function generateToken(string $secret, int $period, int $digits = 6, string $algo = 'sha1'): string
    {
        if ($period < 0) {
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

        $binary = self::packCounterBE($period);
        $hmac   = self::hmac($algo, $key, $binary);
        $code   = self::dynamicTruncate($hmac, $digits);
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
        $offset  = ord($hmac[19]) & 0x0F;
        $binCode = (ord($hmac[$offset]) & 0x7F) << 24
            | (ord($hmac[$offset + 1]) & 0xFF) << 16
            | (ord($hmac[$offset + 2]) & 0xFF) << 8
            | (ord($hmac[$offset + 3]) & 0xFF);
        return bcmod($binCode, 10 ** $digits);
    }

    /**
     * Validate a time-based one-time password (TOTP) token against a shared secret.
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
    public static function validate(string $secret, string $token, int $period, int $digits = 6, string $algo = 'sha1', int $window = 1): bool
    {
        for ($i = 0; $i <= $window; $i++) {
            $candidate = self::generateToken($secret, $period + $i, $digits, $algo);
            if (hash_equals($candidate, self::normalizeToken($token, $digits))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate an HOTP authentication URI for a given account and secret.
     *
     * This method creates a URI instance of type 'hotp' with the specified account,
     *  optional issuer, secret, and HOTP parameters including counter, number of digits,
     *  and hashing algorithm. The resulting URI can be used with authenticator apps
     *  that support HOTP.
     *
     * @param string      $secret  The shared secret for HOTP generation (Base32-encoded).
     * @param string      $account The account name or identifier.
     * @param string|null $issuer  Optional issuer or service provider name.
     * @param int         $period  The initial counter value (default: 0).
     * @param int         $digits  The number of digits in the generated token (default: 6).
     * @param string      $algo    The hashing algorithm (default: 'sha1').
     *
     * @return URI
     */
    public static function getAuthUri(string $secret, string $account, string $issuer = null, int $period = 0, int $digits = 6, string $algo = 'sha1'): URI
    {
        return self::generateURI('hotp', $account, $issuer)->push([
            'secret'    => $secret,
            'counter'   => $period,
            'digits'    => $digits,
            'algorithm' => $algo,
        ]);
    }
}
