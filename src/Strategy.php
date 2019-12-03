<?php
declare (strict_types=1);

namespace csv;

use csv\encoding\Encoder;
use InvalidArgumentException;

class Strategy
{

    public const DEFAULT_DELIMITER = ',';
    public const DEFAULT_ENCLOSURE = '"';
    public const DEFAULT_ESCAPE = '\\';
    public const DEFAULT_ENCODING = Encoder::DEFAULT_ENCODING;

    /**
     * @var string
     */
    private $delimiter;
    /**
     * @var string
     */
    private $enclosure;
    /**
     * @var string
     */
    private $encoding;
    /**
     * @var string
     */
    private $escape;
    /**
     * @var bool
     */
    private $hasHeader;
    /**
     * @var bool
     */
    private $skipEmptyLines;
    /**
     * @var int
     */
    private $skipLeadingLinesCount;
    /**
     * @var bool
     */
    private $asAssociative;

    /**
     * private Strategy constructor to force use of static method createStrategy
     * @param string $delimiter - delimiter for csv
     * @param string $enclosure - enclosure string
     * @param string $escape - escape string
     * @param string $encoding - encoding of csv File
     * @param bool $asAssociative - rows as associative record instead of index based
     * @param bool $hasHeader - csv file has header
     * @param bool $skipEmptyLines - skip the empty lines
     * @param int $skipLeadingLinesCount - skip the n leading lines
     */
    private function __construct(
        string $delimiter = self::DEFAULT_DELIMITER,
        string $enclosure = self::DEFAULT_ENCLOSURE,
        string $escape = self::DEFAULT_ESCAPE,
        string $encoding = self::DEFAULT_ENCODING,
        bool $asAssociative = false,
        bool $hasHeader = true,
        bool $skipEmptyLines = true,
        int $skipLeadingLinesCount = 0
    ) {
        $this->setDelimiter($delimiter);
        $this->setEnclosure($enclosure);
        $this->setEscape($escape);
        $this->setEncoding($encoding);
        $this->setHasHeader($hasHeader);
        $this->setSkipEmptyLines($skipEmptyLines);
        $this->setSkipLeadingLinesCount($skipLeadingLinesCount);
        $this->setAsAssociative($asAssociative);
    }

    /**
     * Creates the standard Strategy
     * @return Strategy
     */
    public static function createStrategy(): self
    {
        return new self();
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * Sets the Delimiter
     * Please use double quotes to ensure that delimiter works as expected
     * @param string $delimiter - One Character delimiter ex. ";", "|", ",", "\t"
     *
     * @return $this
     */
    public function setDelimiter(string $delimiter): self
    {
        if (1 !== strlen($delimiter) || "\n" === $delimiter) {
            throw new InvalidArgumentException('Delimiter must be only one character and not a new line.');
        }
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     *
     * @return $this
     */
    public function setEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding): self
    {
        Encoder::claimValidEncoding($encoding);
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getEscape(): string
    {
        return $this->escape;
    }

    /**
     * @param string $escape
     *
     * @return $this
     */
    public function setEscape(string $escape): self
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * @return int
     */
    public function getSkipLeadingLinesCount(): int
    {
        return $this->skipLeadingLinesCount;
    }

    /**
     * @return bool
     */
    public function hasHeader(): bool
    {
        return $this->hasHeader;
    }

    /**
     * @return bool
     */
    public function doSkipEmptyLines(): bool
    {
        return $this->skipEmptyLines;
    }

    /**
     * @param bool $hasHeader
     *
     * @return $this
     */
    public function setHasHeader(bool $hasHeader): self
    {
        $this->hasHeader = $hasHeader;

        return $this;
    }

    /**
     * @param int $skipLeadingLinesCount
     *
     * @return $this
     */
    public function setSkipLeadingLinesCount(int $skipLeadingLinesCount): self
    {
        $this->skipLeadingLinesCount = $skipLeadingLinesCount;

        return $this;
    }

    /**
     * @param bool $skipEmptyLines
     *
     * @return $this
     */
    public function setSkipEmptyLines(bool $skipEmptyLines): self
    {
        $this->skipEmptyLines = $skipEmptyLines;

        return $this;
    }

    /**
     * @return bool
     */
    public function asAssociative(): bool
    {
        return $this->asAssociative;
    }

    /**
     * @param bool $asAssociative
     *
     * @return $this
     */
    public function setAsAssociative(bool $asAssociative): self
    {
        $this->asAssociative = $asAssociative;
        return $this;
    }
}