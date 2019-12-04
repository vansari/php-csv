<?php
declare (strict_types=1);

namespace csv;

use Faker\Factory;
use PHPUnit\Framework\TestCase;

/**
 * Class WriterTest
 * @package csv
 * @coversDefaultClass \csv\Writer
 */
class WriterTest extends TestCase
{

    /** @var array */
    private $generatedFiles = [];

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->generatedFiles as $generatedFile) {
            if (file_exists($generatedFile)) {
                unlink($generatedFile);
            }
        }
    }

    public function testWriteCsvAndAppendRecords(): void
    {
        $faker = Factory::create('de_DE');
        $records = [
            [
                'id',
                'name',
                'firstname',
                'street',
                'city'
            ],
        ];

        for ($i = 0; $i < 10; $i++) {
            $records[] = [
                'id' => mt_rand(0, 200),
                'name' => $faker->name,
                'firstname' => $faker->firstName,
                'street' => $faker->streetAddress,
                'city' => $faker->city,
            ];
        }
        $this->assertCount(11, $records);
        $writer = new Writer(__DIR__);
        $this->assertFileExists($writer->getTempCsvFilePath());
        $writer->setHeader(array_shift($records));
        $writer->writeRecordsToCsv($records);
        $this->generatedFiles[] = ($csvFilePath = $writer->getCsvFilePath());
        $this->assertFileExists($csvFilePath);
        $recordCount = (new Reader($csvFilePath))->setStrategy($writer->getStrategy())->getRecordCount();
        $this->assertSame(count($records), $recordCount);
        $writerAppend = new Writer(
            pathinfo($csvFilePath, PATHINFO_DIRNAME),
            pathinfo($csvFilePath, PATHINFO_BASENAME),
            true
        );

        $appendRecord = [];
        for ($i = 0; $i < 10; $i++) {
            $appendRecord[] = [
                'id' => mt_rand(0, 200),
                'name' => $faker->name,
                'firstname' => $faker->firstName,
                'street' => $faker->streetAddress,
                'city' => $faker->city,
            ];
        }

        $writerAppend->writeRecordsToCsv($appendRecord);
        $this->assertSame($csvFilePath, $writerAppend->getCsvFilePath());
        $recordsAfterAppend = (new Reader($csvFilePath))->setStrategy($writerAppend->getStrategy())->getRecordCount();
        $this->assertSame(count(array_merge($records, $appendRecord)), $recordsAfterAppend);
    }

    /**
     * @covers ::writeRecordsToCsv
     */
    public function testWriteRecordsToCsv(): void
    {
        $faker = Factory::create('de_DE');
        $records = [
            [
                'id',
                'name',
                'firstname',
                'street',
                'city'
            ],
        ];

        for ($i = 0; $i < 10; $i++) {
            $records[] = [
                'id' => mt_rand(0, 200),
                'name' => $faker->name,
                'firstname' => $faker->firstName,
                'street' => $faker->streetAddress,
                'city' => $faker->city,
            ];
        }
        $this->assertCount(11, $records);
        $writer = new Writer(__DIR__);
        $this->assertFileExists($writer->getTempCsvFilePath());
        $writer->setHeader(array_shift($records));
        $writer->writeRecordsToCsv($records);
        $this->generatedFiles[] = ($csvFilePath = $writer->getCsvFilePath());
        $this->assertFileExists($csvFilePath);

        $reader = new Reader($csvFilePath);
        $reader->getStrategy()->setAsAssociative(true);
        $readRecord = $reader->readAllRecords();
        $this->assertEquals($records, $readRecord);
    }

    /**
     * @covers ::writeRecord
     */
    public function testWriteRecordWithoutHeader(): void
    {
        $writer = new Writer(__DIR__);
        $writer->getStrategy()->setHasHeader(false);
        $record = [
            'column1' => 'foo',
            'column2' => 1.2345,
            'column3' => (new \DateTime())->format('Y-m-d'),
            'column4' => 1234,
        ];

        $this->assertFileExists($writer->getTempCsvFilePath());
        $this->assertTrue($writer->writeRecord($record));
        $reader = new Reader($writer->getTempCsvFilePath());
        $reader->getStrategy()->setHasHeader(false);
        $readRecord = $reader->readRecord();
        $this->assertEquals(array_values($record), $readRecord);
    }

    /**
     * @covers ::writeRecord
     */
    public function testWriteRecordWithHeader(): void
    {
        $writer = new Writer(__DIR__);
        $record = [
            'column1' => 'foo',
            'column2' => 1.2345,
            'column3' => (new \DateTime())->format('Y-m-d'),
            'column4' => 1234,
        ];

        $writer->setHeader(array_keys($record));

        $this->assertFileExists($writer->getTempCsvFilePath());
        $this->assertTrue($writer->writeRecord($record));
        $reader = new Reader($writer->getTempCsvFilePath());
        $reader->getStrategy()->setAsAssociative(true);
        $readRecord = $reader->readRecord();
        $this->assertEquals($record, $readRecord);
    }

    /**
     * @covers ::writeRecords
     */
    public function testWriteRecords(): void
    {
        $faker = Factory::create('de_DE');
        $records = [
            [
                'id',
                'name',
                'firstname',
                'street',
                'city'
            ],
        ];

        for ($i = 0; $i < 10; $i++) {
            $records[] = [
                'id' => mt_rand(0, 200),
                'name' => $faker->name,
                'firstname' => $faker->firstName,
                'street' => $faker->streetAddress,
                'city' => $faker->city,
            ];
        }
        $this->assertCount(11, $records);
        $writer = new Writer(__DIR__);
        $writer->setHeader(array_shift($records));
        $this->assertTrue($writer->writeRecords($records));

        $this->assertFileExists($writer->getTempCsvFilePath());
        $reader = new Reader($writer->getTempCsvFilePath());
        $reader->getStrategy()->setAsAssociative(true);
        $readRecord = $reader->readAllRecords();
        $this->assertEquals($records, $readRecord);
    }

    /**
     * @testdox Test to write 100,000 Records into CSV and read them
     * @covers ::writeRecordsToCsv
     * @throws CsvException
     */
    public function testWriteMassiveCsvData(): void
    {
        $faker = Factory::create('de_DE');
        $records = [
            [
                'id',
                'name',
                'firstname',
                'street',
                'city'
            ],
        ];

        for ($i = 0; $i < 100000; $i++) {
            $records[] = [
                'id' => mt_rand(0, 200),
                'name' => $faker->name,
                'firstname' => $faker->firstName,
                'street' => $faker->streetAddress,
                'city' => $faker->city,
            ];
        }
        $this->assertCount(100001, $records);
        $start = microtime(true);
        $writer = new Writer(__DIR__);
        $this->assertFileExists($writer->getTempCsvFilePath());
        $writer->setHeader(array_shift($records));
        $writer->writeRecordsToCsv($records);
        $end = microtime(true);
        $runtime = $end - $start;
        $this->assertTrue(
            5 > $runtime,
            'The massive CSV Data Writing must be faster then 5 sek. (Runtime: %.2f)'
        );
        $this->generatedFiles[] = ($csvFilePath = $writer->getCsvFilePath());
        $this->assertFileExists($csvFilePath);
    }
}
