<?php

namespace Nacosvel\Authenticator\Contracts;

interface URI
{
    public const HOTP = 'hotp';
    public const TOTP = 'totp';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type): static;

    /**
     * @return string|null
     */
    public function getIssuer(): ?string;

    /**
     * @param string|null $issuer
     *
     * @return static
     */
    public function setIssuer(string $issuer = null): static;

    /**
     * @return string
     */
    public function getAccount(): string;

    /**
     * @param string $account
     *
     * @return static
     */
    public function setAccount(string $account): static;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return array
     */
    public function getQuery(): array;

    /**
     * @param array $query
     *
     * @return static
     */
    public function setQuery(array $query): static;

    /**
     * @return string
     */
    public function getScheme(): string;

    /**
     * @param string $scheme
     *
     * @return static
     */
    public function setScheme(string $scheme): static;

    /**
     * @param string $default
     *
     * @return string
     */
    public function getAlgorithm(string $default = 'SHA1'): string;

    /**
     * @param string $algorithm
     *
     * @return static
     */
    public function algorithm(string $algorithm): static;

    /**
     * @param int $default
     *
     * @return int
     */
    public function getDigits(int $default = 6): int;

    /**
     * @param string $digits
     *
     * @return static
     */
    public function digits(string $digits): static;

    /**
     * @param string|null $default
     *
     * @return string|null
     */
    public function getSecret(string $default = null): ?string;

    /**
     * @param string $secret
     *
     * @return static
     */
    public function secret(string $secret): static;

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Add one or more key-value pairs into the query array.
     *
     * @param string|array $key
     * @param mixed|null   $value
     *
     * @return static
     */
    public function push(string|array $key, mixed $value = null): static;

    /**
     * Check if a given key exists in the query array.
     * This method returns true if the specified key is present and its value is not null.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Create a new instance from an `otpauth` URI string.
     *
     * @param string $uri           The `otpauth` URI to parse.
     * @param string $defaultScheme The expected URI scheme (default: "otpauth").
     *
     * @return static
     */
    public static function fromString(string $uri, string $defaultScheme = 'otpauth'): static;

    /**
     * Generate a new URI instance for a given account and type.
     *
     * This factory method creates a URI object with the specified type, account,
     *  optional issuer, and additional options.
     *
     * @param string      $type    The type of the URI (e.g., 'totp' or 'hotp').
     * @param string      $account The account name or identifier.
     * @param string|null $issuer  Optional issuer or service provider name.
     * @param array       $options Additional options for the URI (e.g., secret, algorithm, digits, period).
     *
     * @return static
     */
    public static function buildURI(string $type, string $account, string $issuer = null, array $options = []): static;

    /**
     * allows a class to decide how it will react when it is treated like a string.
     *
     * @return string
     */
    public function toString(): string;
}
