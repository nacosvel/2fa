<?php

namespace Nacosvel\Authenticator;

use InvalidArgumentException;

final class Base32
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Base32 (RFC 4648) encode. Produces uppercase output with optional padding '='.
     *
     * @param string $binary  raw bytes
     * @param bool   $padding add '=' padding (optional)
     *
     * @return string
     */
    public static function encode(string $binary, bool $padding = false): string
    {
        if ($binary === '') {
            return '';
        }

        $bits = '';
        for ($i = 0, $len = strlen($binary); $i < $len; $i++) {
            $bits .= str_pad(decbin(ord($binary[$i])), 8, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 5) as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $output .= self::ALPHABET[bindec($chunk)];
        }

        if ($padding) {
            $output .= str_repeat('=', ($remainder = strlen($output) % 8) ? 8 - $remainder : 0);
        }

        return $output;
    }

    /**
     * Base32 (RFC 4648) decode. Accepts upper/lowercase, spaces, padding '='.
     *
     * @param string $base32
     *
     * @return string
     * @throws InvalidArgumentException when invalid characters are present
     */
    public static function decode(string $base32): string
    {
        $clean = strtoupper($base32);
        $clean = str_replace(['\x20', ' '], '', $clean);
        $clean = rtrim($clean, '=');
        if ($clean === '') {
            return '';
        }

        $bits     = '';
        $alphabet = array_flip(str_split(self::ALPHABET));
        $len      = strlen($clean);
        for ($i = 0; $i < $len; $i++) {
            $ch = $clean[$i];
            if (!isset($alphabet[$ch])) {
                throw new InvalidArgumentException("Invalid Base32 character: {$ch}");
            }
            $bits .= str_pad(decbin($alphabet[$ch]), 5, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $output .= chr(bindec($chunk));
            }
        }

        return $output;
    }
}
