<?php
declare (strict_types = 1);

namespace vansari\csv;

use vansari\csv\encoding\Encoder;

class Strategy {

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
     * Strategy constructor.
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param string $encoding
     * @param bool $hasHeader
     * @param bool $skipEmptyLines
     * @param int $skipLeadingLinesCount
     */
    public function __construct(
        string $delimiter = self::DEFAULT_DELIMITER,
        string $enclosure = self::DEFAULT_ENCLOSURE,
        string $escape = self::DEFAULT_ESCAPE,
        string $encoding = self::DEFAULT_ENCODING,
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
    }

    /**
     * @return string
     */
    public function getDelimiter(): string {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     *
     * @return $this
     */
    public function setDelimiter(string $delimiter): self {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure(): string {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     *
     * @return $this
     */
    public function setEnclosure(string $enclosure): self {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding(): string {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding): self {
        Encoder::claimValidEncoding($encoding);
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getEscape(): string {
        return $this->escape;
    }

    /**
     * @param string $escape
     *
     * @return $this
     */
    public function setEscape(string $escape): self {
        $this->escape = $escape;

        return $this;
    }

    /**
     * @return int
     */
    public function getSkipLeadingLinesCount(): int {
        return $this->skipLeadingLinesCount;
    }

    /**
     * @return bool
     */
    public function hasHeader(): bool {
        return $this->hasHeader;
    }

    /**
     * @return bool
     */
    public function doSkipEmptyLines(): bool {
        return $this->skipEmptyLines;
    }

    /**
     * @param bool $hasHeader
     *
     * @return $this
     */
    public function setHasHeader(bool $hasHeader): self {
        $this->hasHeader = $hasHeader;

        return $this;
    }

    /**
     * @param int $skipLeadingLinesCount
     *
     * @return $this
     */
    public function setSkipLeadingLinesCount(int $skipLeadingLinesCount): self {
        $this->skipLeadingLinesCount = $skipLeadingLinesCount;

        return $this;
    }

    /**
     * @param bool $skipEmptyLines
     *
     * @return $this
     */
    public function setSkipEmptyLines(bool $skipEmptyLines): self {
        $this->skipEmptyLines = $skipEmptyLines;

        return $this;
    }
}