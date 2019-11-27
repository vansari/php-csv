<?php
declare (strict_types=1);


namespace vansari\csv\util;

use InvalidArgumentException;
use vansari\csv\encoding\Encoder;

/**
 * Class HeaderNormalizer - Normalize header of CSV
 * @package vansari\csv\util
 */
class HeaderNormalizer
{
    public const DEFAULT_PATTERNS = [
        '/ä/',
        '/ü/',
        '/ö/',
        '/ß/',
        '/\W+/',
        '/_+/',
        '/(^_|_$)/',
    ];

    public const DEFAULT_REPLACEMENTS = ['ae', 'ue', 'oe', 'ss', '_', '_', '',];

    /** @var array $patterns */
    private $patterns = self::DEFAULT_PATTERNS;

    /** @var array $replacements */
    private $replacements = self::DEFAULT_REPLACEMENTS;

    /**
     * HeaderNormalizer constructor.
     * @param array|null $patterns
     * @param array|null $replacements
     * @codeCoverageIgnore
     */
    public function __construct(?array $patterns = null, ?array $replacements = null)
    {
        $this->patterns = $patterns ?? HeaderNormalizer::DEFAULT_PATTERNS;
        $this->replacements = $replacements ?? HeaderNormalizer::DEFAULT_REPLACEMENTS;
    }

    /**
     * Add custom patterns and replacements to end of the defaults
     * @param array $pattern
     * @param array $replacements
     * @return $this
     */
    public function pushPatternReplacement(array $pattern, array $replacements): self
    {
        if (count($pattern) !== count($replacements)) {
            throw new InvalidArgumentException('Patterns and Replacements must have the same count of values.');
        }
        $this->patterns = array_merge($this->patterns, $pattern);
        $this->replacements = array_merge($this->replacements, $replacements);

        return $this;
    }

    /**
     * Add custom patterns and replacements at the begin of the defaults
     * @param array $pattern
     * @param array $replacements
     * @return $this
     */
    public function unshiftPatternReplacement(array $pattern, array $replacements): self
    {
        if (count($pattern) !== count($replacements)) {
            throw new InvalidArgumentException('Patterns and Replacements must have the same count of values.');
        }
        $this->patterns = array_merge($pattern, $this->patterns);
        $this->replacements = array_merge($replacements, $this->replacements);

        return $this;
    }

    /**
     * Replace default pattern and replacement
     * @param array $pattern
     * @param array $replacements
     * @return $this
     */
    public function setPatternReplacement(array $pattern, array $replacements): self
    {
        if (count($pattern) !== count($replacements)) {
            throw new InvalidArgumentException('Patterns and Replacements must have the same count of values.');
        }
        $this->patterns = $pattern;
        $this->replacements = $replacements;

        return $this;
    }

    /**
     * Normalize the header with the given replacement
     * @param array $header
     * @return array
     */
    public function normalizeHeader(array $header): array
    {
        return array_map([$this, 'normalize'], $header);
    }

    /**
     * @param string $item
     * @return string
     */
    public function normalize(string $item): string
    {
        return preg_replace(
            $this->patterns,
            $this->replacements,
            $item
        );
    }
}