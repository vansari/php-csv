<?php
declare (strict_types = 1);

namespace vansari\csv;

use Iterator;
use SplFileObject;

/**
 * Class Reader
 * @package vansari\csv
 * @link https://tools.ietf.org/html/rfc4180
 */
class Reader implements Iterator {

    public const EXT_CSV = 'csv';
    public const EXT_TXT = 'txt';

    private const SUPPORTED_EXT = [
        self::EXT_CSV,
        self::EXT_TXT,
    ];

    /**
     * @var array|false|null
     */
    private $currentRecord;

    /**
     * @var array
     */
    private $header = [];

    /**
     * @var SplFileObject
     */
    private $fileObject;
    /**
     * @var bool
     */
    private $headerRead = false;
    /**
     * @var bool
     */
    private $linesSkipped = false;

    /**
     * @var Strategy
     */
    private $strategy;

    /**
     * @var int
     */
    private $rowIndex = 0;

    /**
     * Reader constructor.
     * @param string $file
     * @param Strategy|null $strategy
     * @throws CsvException
     */
    public function __construct(string $file, ?Strategy $strategy = null) {
        $this->setFileObject($file);
        $this->strategy = $strategy ?? new Strategy();
    }

    private function headerRead(): void {
        if (false === $this->getStrategy()->hasHeader() || true === $this->headerRead) {
            return;
        }

        $this->rewind();
        if (false === $this->linesSkipped) {
            $skip = $this->getStrategy()->getSkipLeadingLinesCount();
            for ($skipped = 0; $skipped < $skip; $skipped++) {
                $this->current();
                $this->next();
            }
            $this->linesSkipped = true;
        }
        $this->header = $this->current();
        $this->headerRead = true;
        $this->next();
    }

    /**
     * @return string
     */
    public function getFile(): string {
        return $this->fileObject->getPathname();
    }

    public function getHeader(): array {
        $this->headerRead();

        return $this->header;
    }

    /**
     * @param int $searchIndex
     * @return array|null
     */
    public function getRecordAtIndex(int $searchIndex): ?array {
        $this->rewind();
        while ($this->valid()) {
            if ($this->key() === $searchIndex) {
                return $this->current();
            }
        }

        return null;
    }

    /**
     * @return Strategy
     */
    public function getStrategy(): Strategy {
        return $this->strategy;
    }

    /**
     * @param string $file
     * @throws CsvException
     */
    public function validateFile(string $file): void {
        if ('' === $file) {
            throw CsvException::paramError(
                'Parameter $file must be an non empty string',
                CsvException::ERROR_PARAM_EMPTY_STRING
            );
        }
        if (false === is_file($file)) {
            throw CsvException::validationError(
                '$file must be an existing file with csv or txt extension',
                CsvException::VALIDATION_ERROR_FILE
            );
        }
        if (false === in_array(pathinfo($file, PATHINFO_EXTENSION), self::SUPPORTED_EXT)) {
            throw CsvException::validationError(
                '$file must have csv or txt extension',
                CsvException::VALIDATION_ERROR_EXT
            );
        }
    }

    /**
     * @param string $file
     *
     * @return $this
     * @throws CsvException
     */
    public function setFileObject(string $file): self {
        $this->validateFile($file);
        $this->fileObject = new SplFileObject($file);
        $this->fileObject->setFlags(SplFileObject::READ_CSV);

        return $this;
    }

    private function convertEncoding(array $row): array {

    }
    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current() {
        return $this->currentRecord = $this->fileObject->fgetcsv(
            $this->getStrategy()->getDelimiter(),
            $this->getStrategy()->getEnclosure(),
            $this->getStrategy()->getEscape()
        );
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next() {
        $this->rowIndex++;
        $this->fileObject->next();
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key() {
        return $this->fileObject->key();
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid() {
        return $this->fileObject->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind() {
        $this->rowIndex = 0;
        $this->fileObject->rewind();
    }

    public function getRecordCount(): int {
        while ($this->valid()) {
            $this->readRecord();
        }

        return ($this->getStrategy()->hasHeader()) ? $this->rowIndex - 1 : $this->rowIndex;
    }

    /**
     * Read the current row and moves the pointer to the next Record
     * @return array
     */
    public function readRecord(): array {
        $this->headerRead();
        $this->current();
        $this->next();
        if ($this->getStrategy()->doSkipEmptyLines()) {
            while ($this->valid() && [] === $this->currentRecord) {
                $this->readRecord();
            }
        }

        return $this->currentRecord;
    }
}