<?php
declare (strict_types = 1);

namespace vansari\csv\encoding;

use InvalidArgumentException;

/**
 * Class Encoder
 * @package vansari\csv\encoding
 */
class Encoder {

    public const DEFAULT_ENCODING = CharsetEncodings::UTF_8;

    /**
     * @param string|array $value
     * @param string|null $targetEncoding
     * @return string
     */
    public static function convertTo($value, ?string $targetEncoding = null): string {
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
     * @return string
     */
    public static function convertFrom($value, ?string $sourceEncoding = null): string {
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
     */
    private static function claimParameter($value): void {
        if (false === is_string($value) || false === is_array($value)) {
            throw new InvalidArgumentException('Parameter must be a string or an array.');
        }
    }

    public static function claimValidEncoding(string $encoding): void {
        if ('' === $encoding || false === in_array($encoding, CharsetEncodings::getConstants())) {
            throw new InvalidArgumentException('Encoding is not valid or a empty string.');
        }
    }
}