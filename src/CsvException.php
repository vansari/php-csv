<?php
declare (strict_types=1);

namespace csv;

use Exception;
use Throwable;

class CsvException extends Exception
{

    public const
        VALIDATION_ERROR_DEFAULT = 10,
        VALIDATION_ERROR_EXT = 11,
        VALIDATION_ERROR_FILE = 12;

    public const
        ERROR_PARAM = 20,
        ERROR_PARAM_EMPTY_STRING = 21;

    public const ERROR_READ = 30;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @return static
     */
    public static function validationError(
        ?string $message = null,
        int $code = self::VALIDATION_ERROR_DEFAULT,
        ?Throwable $previous = null
    ): self {
        return new self($message ?? 'An error occurred at validation.', $code, $previous);
    }

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @return static
     */
    public static function paramError(
        ?string $message = null,
        int $code = self::ERROR_PARAM,
        ?Throwable $previous = null
    ): self {
        return new self($message ?? 'Invalid parameter.', $code, $previous);
    }

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previousException
     * @return static
     */
    public static function readError(
        ?string $message = null,
        int $code = self::ERROR_READ,
        ?Throwable $previousException = null
    ): self {
        return new self($message ?? 'Unknown Error while reading the record.', $code, $previousException);
    }
}