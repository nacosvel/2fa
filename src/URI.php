<?php

namespace Nacosvel\Authenticator;

use InvalidArgumentException;

class URI implements Contracts\URI
{
    public function __construct(
        protected string $type,
        protected ?string $issuer,
        protected string $account,
        protected array $query = [],
        protected string $scheme = 'otpauth',
    ) {
        $this->query = array_filter(
            array_merge($query, compact('issuer')),
            static fn($v) => is_null($v) === false
        );
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIssuer(): ?string
    {
        return $this->issuer ? rawurldecode($this->issuer) : null;
    }

    /**
     * @param string|null $issuer
     *
     * @return static
     */
    public function setIssuer(string $issuer = null): static
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccount(): string
    {
        return rawurldecode($this->account);
    }

    /**
     * @param string $account
     *
     * @return static
     */
    public function setAccount(string $account): static
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getIssuer()
            ? rawurlencode($this->getIssuer()) . ':' . rawurlencode($this->getAccount())
            : rawurlencode($this->getAccount());
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     *
     * @return static
     */
    public function setQuery(array $query): static
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     *
     * @return static
     */
    public function setScheme(string $scheme): static
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @param string $default
     *
     * @return string
     */
    public function getAlgorithm(string $default = 'SHA1'): string
    {
        return $this->get('algorithm', $default);
    }

    /**
     * @param string $algorithm
     *
     * @return static
     */
    public function algorithm(string $algorithm): static
    {
        return $this->push('algorithm', $algorithm);
    }

    /**
     * @param int $default
     *
     * @return int
     */
    public function getDigits(int $default = 6): int
    {
        return $this->get('digits', $default);
    }

    /**
     * @param string $digits
     *
     * @return static
     */
    public function digits(string $digits): static
    {
        return $this->push('digits', $digits);
    }

    /**
     * @param string|null $default
     *
     * @return string|null
     */
    public function getSecret(string $default = null): ?string
    {
        return $this->get('secret', $default);
    }

    /**
     * @param string $secret
     *
     * @return static
     */
    public function secret(string $secret): static
    {
        return $this->push('secret', $secret);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Add one or more key-value pairs into the query array.
     *
     * @param string|array $key
     * @param mixed|null   $value
     *
     * @return static
     */
    public function push(string|array $key, mixed $value = null): static
    {
        $this->query = (is_array($key) ? $key : [$key => $value]) + $this->query;

        return $this;
    }

    /**
     * Check if a given key exists in the query array.
     * This method returns true if the specified key is present and its value is not null.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->query[$key]);
    }

    /**
     * Create a new instance from an `otpauth` URI string.
     *
     * @param string $uri           The `otpauth` URI to parse.
     * @param string $defaultScheme The expected URI scheme (default: "otpauth").
     *
     * @return static
     */
    public static function fromString(string $uri, string $defaultScheme = 'otpauth'): static
    {
        ($urls = parse_url($uri)) || throw new InvalidArgumentException('Invalid URI.');

        foreach (['scheme', 'host', 'path', 'query'] as $key) {
            array_key_exists($key, $urls) || throw new InvalidArgumentException(
                "Invalid URI: lacks the `{$key} field."
            );
        }

        [
            'scheme' => $scheme,
            'host'   => $host,
            'path'   => $path,
            'query'  => $query,
        ] = parse_url($uri);

        strtolower($scheme) === strtolower($defaultScheme) || throw new InvalidArgumentException(
            "Invalid URI: invalid `scheme` field."
        );

        $path  = rawurldecode(trim($path, '/'));
        $paths = explode(':', "{$path}", 2);
        [$issuer, $account] = count($paths) === 1 ? [null, $paths[0]] : $paths;

        $params = [];
        parse_str($query, $params);
        $params = array_change_key_case($params);
        array_key_exists('secret', $params) || throw new InvalidArgumentException(
            "Invalid URI: invalid `secret` field."
        );

        return new static($host, $issuer, $account, $params, $scheme);
    }

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
    public static function buildURI(string $type, string $account, string $issuer = null, array $options = []): static
    {
        if (class_exists($type)) {
            $type = basename(str_replace('\\', '/', $type));
            $type = strtolower($type);
        }

        return new static($type, $issuer, $account, $options);
    }

    /**
     * allows a class to decide how it will react when it is treated like a string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * Magic method {@see https://www.php.net/manual/en/language.oop5.magic.php#object.tostring}
     * allows a class to decide how it will react when it is treated like a string.
     *
     * @return string Returns string representation of the object that
     * implements this interface (and/or "__toString" magic method).
     */
    #[\Override]
    public function __toString(): string
    {
        $query = array_filter($this->getQuery(), static fn($v) => $v !== null && $v !== '');
        $query = http_build_query($query, arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);
        return sprintf('%s://%s/%s?%s', $this->getScheme(), $this->getType(), $this->getLabel(), $query);
    }
}
