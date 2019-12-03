<?php
declare (strict_types=1);

namespace csv;

use csv\encoding\Encoder;
use csv\util\HeaderNormalizer;
use Exception;
use InvalidArgumentException;
use SplFileObject;

class Writer
{
    private $strategy;

    /**
     * @var SplFileObject
     */
    private $fileObject;
    /**
     * @var array
     */
    private $header = [];

    /**
     * @var HeaderNormalizer
     */
    private $normalizer = null;

    /**
     * @var bool
     */
    private $normalizeHeader = false;
    /**
     * @var bool
     */
    private $headerWrote;
    /**
     * @var string
     */
    private $file;
    /**
     * @var string
     */
    private $targetPath;

    /**
     * Writer constructor.
     * @param string $filename
     * @param string $targetPath
     * @throws Exception
     */
    public function __construct(string $targetPath, ?string $filename = null)
    {
        $this->strategy = Strategy::createStrategy();
        $this->setFileObject($filename);
        $this->targetPath = $targetPath;
    }

    /**
     * @return Strategy
     */
    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }

    /**
     * Returns only the CSV File path but it isn't secure that the File exists there
     * @return string
     */
    public function getCsvFilePath(): string
    {
        return $this->targetPath . DIRECTORY_SEPARATOR . $this->fileObject->getFilename();
    }

    /**
     * @param string|null $filename
     * @return $this
     * @throws Exception
     */
    private function setFileObject(?string $filename = null): self
    {
        if (null === $filename || '' === $filename) {
            $filename = (new \DateTime())->format('Ymd_his') . '_' . uniqid(mt_rand(0, 200)) . '.csv';
        }
        $filepath = sys_get_temp_dir();
        $filepath .= DIRECTORY_SEPARATOR . $filename;
        $this->fileObject = new SplFileObject($filepath, 'w+');

        return $this;
    }

    /**
     * Get the temp File Path or create them first and return it
     * @return string
     * @throws Exception
     */
    public function getTempCsvFilePath(): string
    {
        return $this->fileObject->getPath();
    }

    /**
     * Sets the Header if necessary
     * @param array $header - array with the named fields of the Header
     * @return $this
     */
    public function setHeader(array $header): self
    {
        if ([] === $header) {
            throw new InvalidArgumentException('Set not an empty Header.');
        }
        $filtered = array_filter(
            $header,
            function ($field): bool {
                return null !== $field && is_string($field) && '' === $field;
            }
        );
        if (count($filtered) != count($header)) {
            throw new InvalidArgumentException('The header was modified while checking fields.');
        }

        $this->header = $header;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNormalizeHeader(): bool
    {
        return $this->normalizeHeader;
    }

    /**
     * @return HeaderNormalizer
     */
    public function getNormalizer(): HeaderNormalizer
    {
        return $this->normalizer;
    }

    /**
     * @param HeaderNormalizer $normalizer
     *
     * @return $this
     */
    public function setNormalizer(HeaderNormalizer $normalizer): self
    {
        $this->normalizeHeader = true;
        $this->normalizer = $normalizer;
        return $this;
    }

    /**
     * Check if we need to normalize Header and if so than normalize it and set them to lower case
     * @param array $header
     * @return array - the original or normilzed header
     */
    private function normalizeHeader(array $header): array
    {
        if ($this->isNormalizeHeader()) {
            return array_map('strtolower', $this->getNormalizer()->normalizeHeader($header));
        }

        return $header;
    }

    /**
     * @param array $row
     * @return array
     */
    private function convertRowFromUtf8(array $row): array
    {
        return Encoder::convertTo($row, $this->getStrategy()->getEncoding());
    }

    /**
     * @throws CsvException
     */
    private function writeHeader(): void
    {
        if ($this->getStrategy()->hasHeader()) {
            if (empty($this->header)) {
                throw new InvalidArgumentException('Tried to write header but it was empty.');
            }

            $wroteLine = $this->fileObject->fputcsv(
                $this->convertRowFromUtf8($this->normalizeHeader($this->header)),
                $this->getStrategy()->getDelimiter(),
                $this->getStrategy()->getEnclosure(),
                $this->getStrategy()->getEscape()
            );

            if (false === $wroteLine) {
                throw CsvException::writeError();
            }
            $this->headerWrote = true;
        }
    }

    /**
     * @param array $records
     * @return bool
     * @throws CsvException
     */
    public function writeRecords(array $records): bool
    {
        foreach ($records as $row) {
            if (false === $this->writeRecord($row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $row
     * @return bool
     * @throws CsvException
     */
    public function writeRecord(array $row): bool
    {
        if ($this->getStrategy()->hasHeader()) {
            if (count($row) !== count($this->header)) {
                throw new InvalidArgumentException(
                    'Record count does not match the header field count: ' . implode(', ', $row)
                );
            }
            if (false === $this->headerWrote) {
                $this->writeHeader();
            }
        }

        $wroteLine = $this->fileObject->fputcsv(
            $this->convertRowFromUtf8($row),
            $this->getStrategy()->getDelimiter(),
            $this->getStrategy()->getEnclosure(),
            $this->getStrategy()->getEscape()
        );

        if (false === $wroteLine) {
            throw CsvException::writeError();
        }

        return 1 === $wroteLine;
    }

    /**
     * Write the records to the CSV TempFile
     * If that was successful than move file from temp directory to target directory
     * @param array $records
     * @throws CsvException
     */
    public function writeRecordsToCsv(array $records): void
    {
        if (false === $this->writeRecords($records)) {
            throw CsvException::writeError();
        }

        if (false === rename($this->getTempCsvFilePath(), $this->getCsvFilePath())) {
            throw new Exception('Could not move file from tmp to target directory.');
        }
    }
}