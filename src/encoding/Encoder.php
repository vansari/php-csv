<?php
declare (strict_types=1);

namespace csv\encoding;

use InvalidArgumentException;

/**
 * Class Encoder
 * @package vansari\csv\encoding
 */
class Encoder
{
    /** @var string Default Encoding of the Encoder */
    public const DEFAULT_ENCODING = CharsetEncodings::UTF_8;

    /**
     * @param string|array $value
     * @param string|null $targetEncoding
     * @return string|array
     */
    public static function convertTo($value, ?string $targetEncoding = null)
    {
        Encoder::claimParameter($value);
        Encoder::claimValidEncoding($targetEncoding);
        return mb_convert_encoding(
            $value,
            $targetEncoding ?? self::DEFAULT_ENCODING,
            self::DEFAULT_ENCODING
        );
    }

    /**
     * @param string|array $value
     * @param string|null $sourceEncoding
     * @return string|array
     */
    public static function convertFrom($value, ?string $sourceEncoding = null)
    {
        Encoder::claimParameter($value);
        Encoder::claimValidEncoding($sourceEncoding);
        return mb_convert_encoding(
            $value,
            self::DEFAULT_ENCODING,
            $sourceEncoding ?? self::DEFAULT_ENCODING
        );
    }

    /**
     * @param string|array $value
     * @throws InvalidArgumentException - if the $value is not a string or array
     */
    private static function claimParameter($value): void
    {
        if (is_string($value) || is_array($value)) {
            return;
        }
        throw new InvalidArgumentException(
            'Parameter must be a string or an array, is: '
            . (is_object($value) ? get_class($value) : gettype($value))
        );
    }

    /**
     * @param string $encoding
     * @throws InvalidArgumentException - if the chosen encoding is not valid
     */
    public static function claimValidEncoding(string $encoding): void
    {
        if ('' === $encoding || false === in_array($encoding, CharsetEncodings::getConstants())) {
            throw new InvalidArgumentException('Encoding is not valid or a empty string.');
        }
    }
}