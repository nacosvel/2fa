<?php

namespace Tests;

use InvalidArgumentException;
use Nacosvel\Authenticator\Base32;
use PHPUnit\Framework\TestCase;

class Base32Test extends TestCase
{
    /**
     * @dataProvider base32Provider
     */
    public function testEncodeDecodeMultipleCases(string $input): void
    {
        $encoded = Base32::encode($input);
        $decoded = Base32::decode($encoded);
        $this->assertEquals($input, $decoded, "Decoding failed for input '{$input}'");
    }

    public static function base32Provider(): array
    {
        return [
            [''], ['f'], ['fo'], ['foo'], ['foob'], ['fooba'], ['foobar'],
        ];
    }

    public function testEncodeDecode()
    {
        $original = "hello world";
        $encoded  = Base32::encode($original);
        $decoded  = Base32::decode($encoded);

        $this->assertIsString($encoded, "Encode should return string");
        $this->assertEquals($original, $decoded, "Decoded value should match original");
    }

    public function testEncodeWithPadding()
    {
        $data    = "foob";
        $encoded = Base32::encode($data, true);
        $this->assertStringEndsWith("=", $encoded, "Encoded string should have padding");
    }

    public function testDecodeInvalidCharacter()
    {
        $this->expectException(InvalidArgumentException::class);
        Base32::decode("INVALID@CHAR");
    }
}
