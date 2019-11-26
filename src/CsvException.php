<?php
declare (strict_types = 1);

namespace vansari\csv;

use Exception;
use Throwable;

class CsvException extends Exception {

    public const
        VALIDATION_ERROR_DEFAULT = 10,
        VALIDATION_ERROR_EXT = 11,
        VALIDATION_ERROR_FILE = 12;

    public const
        ERROR_PARAM = 20,
        ERROR_PARAM_EMPTY_STRING = 21;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @return static
     */
    public static function validationError(
        string $message = 'An error occurred at validation.',
        int $code = self::VALIDATION_ERROR_DEFAULT,
        ?Throwable $previous = null
    ): self {
        return new self($message, $code, $previous);
    }

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @return static
     */
    public static function paramError(
        string $message = 'Invalid parameter.',
        int $code = self::ERROR_PARAM,
        ?Throwable $previous = null
    ): self {
        return new self($message, $code, $previous);
    }
}