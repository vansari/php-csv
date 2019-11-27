<?php
declare (strict_types=1);

namespace vansari\csv;

use Iterator;
use OutOfRangeException;
use SplFileObject;
use vansari\csv\encoding\Encoder;
use vansari\csv\util\HeaderNormalizer;

/**
 * Class Reader
 * @package vansari\csv
 * @link https://tools.ietf.org/html/rfc4180
 */
class Reader implements Iterator
{

    public const EXT_CSV = 'csv';
    public const EXT_TXT = 'txt';

    private const SUPPORTED_EXT = [
        self::EXT_CSV,
        self::EXT_TXT,
    ];

    /**
     * @var array|false|null
     */
    private $currentRecord = null;

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

    /** @var bool $normalizeHeader - convert Header
     */
    private $normalizeHeader = false;
    /**
     * @var HeaderNormalizer
     */
    private $normalizer = null;

    /**
     * Reader constructor.
     * @param string $file
     * @param Strategy|null $strategy
     * @throws CsvException
     */
    public function __construct(string $file)
    {
        $this->setFileObject($file);
        $this->strategy = Strategy::createStrategy();
    }

    /**
     * Read the Header first (if not already read), convert it to UTF-8 and set the pointer to next row
     * It is possible to skip leading lines in example the file contains empty or unnecessary lines
     */
    private function headerRead(): void
    {
        if (false === $this->getStrategy()->hasHeader() || true === $this->headerRead) {
            return;
        }

        $this->rewind();
        $this->skipLeadingLines();
        $this->header = $this->normalizeHeader($this->convertRowToUtf8($this->current()));
        $this->headerRead = true;
        $this->next();
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->fileObject->getPathname();
    }

    /**
     * Returns the Header
     * @return array
     */
    public function getHeader(): array
    {
        $this->headerRead();

        return $this->header;
    }

    /**
     * Returns the utf-8 converted record from the specified index
     * @param int $lineIndex - zero based $rowIndex without header
     * @return array|null
     * @throws CsvException
     */
    public function readRecordAtIndex(int $lineIndex): ?array
    {
        if (0 > $lineIndex) {
            throw new OutOfRangeException('$lineIndex must be a non negativ Integer.');
        }
        if (($rowCount = $this->getRecordCount()) < $lineIndex) {
            throw new OutOfRangeException(
                '$lineIndex is greater than the row count. File contains ' . $rowCount . ' rows.'
            );
        }

        $this->rewind();
        $this->setRecordPointerToIndex($lineIndex);

        return $this->readRecord();
    }

    /**
     * Returns all Records between the given start line and the given endline
     * @param int $rowIndexStart - start row index
     * @param int $rowIndexStop - end row index
     * @return array
     * @throws CsvException - if an error occurred while reading
     */
    public function readRecordsOfRange(int $rowIndexStart, int $rowIndexStop): array
    {
        if (0 > $rowIndexStart) {
            throw new OutOfRangeException('$rowIndexStart must be a non negativ Integer.');
        }
        if (($rowCount = $this->getRecordCount()) < $rowIndexStop) {
            throw new OutOfRangeException(
                '$rowIndexStop is greater than the row count. File contains ' . $rowCount . ' rows.'
            );
        }
        $records = [];
        // Set the record pointer to the start index
        $this->setRecordPointerToIndex($rowIndexStart);

        do {
            $records[] = $this->readRecord();
        } while ($this->valid() && $this->getRowIndex() !== $rowIndexStop);

        return $records;
    }

    /**
     * @param bool $asAssociative
     * @return array
     * @throws CsvException
     */
    public function readAllRecords(): array
    {
        $records = [];
        $this->rewind();
        while ($this->valid()) {
            $records[] = $this->readRecord();
        }

        return $records;
    }

    /**
     * @return Strategy
     */
    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }

    /**
     * @param string $file
     * @throws CsvException
     */
    public function validateFile(string $file): void
    {
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
    public function setFileObject(string $file): self
    {
        $this->validateFile($file);
        $this->fileObject = new SplFileObject($file);
        $this->fileObject->setFlags(SplFileObject::READ_CSV);

        return $this;
    }

    /**
     * @param array $row
     * @return array
     */
    private function convertRowToUtf8(array $row): array
    {
        return Encoder::convertFrom($row, $this->getStrategy()->getEncoding());
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->fileObject->fgetcsv(
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
    public function next()
    {
        $this->rowIndex++;
        $this->fileObject->next();
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->fileObject->key();
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->fileObject->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->rowIndex = 0;
        $this->fileObject->rewind();
    }

    /**
     * @return int
     * @throws CsvException
     */
    public function getRecordCount(): int
    {
        while ($this->valid()) {
            $this->readRecord();
        }

        return ($this->getStrategy()->hasHeader()) ? $this->rowIndex - 1 : $this->rowIndex;
    }

    /**
     * Read the current row and convert them to UTF-8 and moves the pointer to the next Record
     * Skip empty lines if it is set
     *
     * @return array
     * @throws CsvException
     */
    public function readRecord(): array
    {
        $this->skipLeadingLines();
        $this->headerRead();
        $this->currentRecord = $this->current();
        if (false === $this->currentRecord) {
            throw CsvException::readError(error_get_last()['message'] ?? null);
        }
        $this->next();
        if ($this->getStrategy()->doSkipEmptyLines()) {
            while ($this->valid() && [] === $this->currentRecord) {
                $this->readRecord();
            }
        }

        if ($this->getStrategy()->hasHeader() && $this->getStrategy()->asAssociative()) {
            $this->currentRecord = array_combine($this->getHeader(), $this->currentRecord);
        }

        return $this->convertRowToUtf8($this->currentRecord);
    }

    /**
     * @return array|false|null
     */
    public function getCurrentRecord()
    {
        return $this->currentRecord;
    }

    /**
     * @param int $index
     * @throws CsvException
     */
    private function setRecordPointerToIndex(int $index): void
    {
        $this->rewind();
        while ($this->valid() && $this->getRowIndex() < $index) {
            $this->readRecord();
        }
    }

    /**
     * @return int
     */
    private function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    /**
     * @return bool
     */
    public function isNormalizeHeader(): bool
    {
        return $this->normalizeHeader;
    }

    /**
     * @param HeaderNormalizer $normalizer
     * @return $this
     */
    public function setNormalizeHeader(HeaderNormalizer $normalizer): self
    {
        $this->normalizeHeader = true;
        $this->normalizer = $normalizer;

        return $this;
    }

    /**
     * Check if we need to normalize Header and if so than normalize it
     * @param array $header
     * @return array - the original or normilzed header
     */
    private function normalizeHeader(array $header): array
    {
        if ($this->isNormalizeHeader()) {
            return $this->normalizer->normalizeHeader($header);
        }

        return $header;
    }

    /**
     * Skip leading lines if it was not done
     */
    public function skipLeadingLines(): void
    {
        if (false === $this->linesSkipped) {
            $this->rewind();
            $skip = $this->getStrategy()->getSkipLeadingLinesCount();
            for ($skipped = 0; $skipped < $skip; $skipped++) {
                $this->current();
                $this->next();
            }
            $this->linesSkipped = true;
        }
    }
}