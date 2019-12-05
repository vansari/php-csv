<?php
declare (strict_types=1);

namespace csv;

use OutOfRangeException;
use PHPUnit\Framework\TestCase;
use Throwable;
use csv\util\HeaderNormalizer;

/**
 * Class ReaderTest
 * @package vansari\csv
 * @coversDefaultClass \csv\Reader
 */
class ReaderTest extends TestCase
{
    private $expectedNormalizedHeader = [
        'emp_id',
        'name_prefix',
        'first_name',
        'middle_initial',
        'last_name',
        'gender',
        'e_mail',
        'father_s_name',
        'mother_s_name',
        'mother_s_maiden_name',
        'date_of_birth',
        'time_of_birth',
        'age_in_yrs',
        'weight_in_kgs',
        'date_of_joining',
        'quarter_of_joining',
        'half_of_joining',
        'year_of_joining',
        'month_of_joining',
        'month_name_of_joining',
        'short_month',
        'day_of_joining',
        'dow_of_joining',
        'short_dow',
        'age_in_company_years',
        'salary',
        'last_hike',
        'ssn',
        'phone_no',
        'place_name',
        'county',
        'city',
        'state',
        'zip',
        'region',
        'user_name',
        'password',
    ];

    /** @var array */
    private $expectedHeader = [
        'Emp ID',
        'Name Prefix',
        'First Name',
        'Middle Initial',
        'Last Name',
        'Gender',
        'E Mail',
        'Father\'s Name',
        'Mother\'s Name',
        'Mother\'s Maiden Name',
        'Date of Birth',
        'Time of Birth',
        'Age in Yrs.',
        'Weight in Kgs.',
        'Date of Joining',
        'Quarter of Joining',
        'Half of Joining',
        'Year of Joining',
        'Month of Joining',
        'Month Name of Joining',
        'Short Month',
        'Day of Joining',
        'DOW of Joining',
        'Short DOW',
        'Age in Company (Years)',
        'Salary',
        'Last % Hike',
        'SSN',
        'Phone No. ',
        'Place Name',
        'County',
        'City',
        'State',
        'Zip',
        'Region',
        'User Name',
        'Password',
    ];

    private $expectedFirstRecord = [
        '677509',
        'Drs.',
        'Lois',
        'H',
        'Walker',
        'F',
        'lois.walker@hotmail.com',
        'Donald Walker',
        'Helen Walker',
        'Lewis',
        '3/29/1981',
        '02:31:49 AM',
        '36.36',
        '60',
        '11/24/2003',
        'Q4',
        'H2',
        '2003',
        '11',
        'November',
        'Nov',
        '24',
        'Monday',
        'Mon',
        '13.68',
        '168251',
        '21%',
        '467-99-4677',
        '303-572-8492',
        'Denver',
        'Denver',
        'Denver',
        'CO',
        '80224',
        'West',
        'lhwalker',
        'DCa}.T}X:v?NP',
    ];

    private $expectedIndexRow = [
        '560455',
        'Ms.',
        'Carolyn',
        'V',
        'Hayes',
        'F',
        'carolyn.hayes@hotmail.co.uk',
        'Jimmy Hayes',
        'Sara Hayes',
        'Foster',
        '3/10/1958',
        '12:52:37 PM',
        '59.42',
        '53',
        '7/3/2001',
        'Q3',
        'H2',
        '2001',
        '7',
        'July',
        'Jul',
        '3',
        'Tuesday',
        'Tue',
        '16.08',
        '42005',
        '14%',
        '730-28-1350',
        '239-882-8784',
        'Saint Cloud',
        'Osceola',
        'Saint Cloud',
        'FL',
        '34771',
        'South',
        'cvhayes',
        'NY!Y2sw.[_v-Q9{',
    ];

    private $expectedValuesFromRange = [
        ['162402',],
        ['231469',],
        ['153989',],
        ['386158',],
        ['301576',],
        ['441771',],
        ['528509',],
        ['912990',],
        ['214352',],
        ['890290',],
        ['622406',],
    ];

    /**
     * @testdox Tests if we can retrieve the Header
     * @covers ::getHeader
     */
    public function testGetHeader(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $header = $reader->getHeader();
        $this->assertSame(
            $this->expectedHeader,
            $header
        );
    }

    /**
     * @testdox Get the Header as normalized array
     * @throws CsvException
     * @covers ::getHeader
     */
    public function testGetHeaderNormalized(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $reader->setNormalizeHeader(new HeaderNormalizer());
        $header = $reader->getHeader();
        $this->assertSame(
            $this->expectedNormalizedHeader,
            $header
        );
    }

    public function testGetHeaderNull(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $reader->getStrategy()->setHasHeader(false);
        $header = $reader->getHeader();
        $this->assertEmpty($header);
    }

    /**
     * @throws CsvException
     * @covers ::getHeader
     */
    public function testGetHeaderFailed(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        // Header did not match if Strategy is set to skip 1 line
        $reader->getStrategy()->setSkipLeadingLinesCount(1);
        $this->assertSame(1, $reader->getStrategy()->getSkipLeadingLinesCount());
        $this->assertNotSame($this->expectedHeader, $reader->getHeader());
    }

    public function testReadRecord(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $this->assertSame($this->expectedFirstRecord, $reader->readRecord());
    }

    public function testReadRecordAsAssociative(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $reader->getStrategy()->setAsAssociative(true);
        $record = $reader->readRecord();
        $this->assertSame($this->expectedFirstRecord, array_values($record));
        $this->assertSame($this->expectedHeader, array_keys($record));
    }

    public function testGetRecordCount(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $this->assertSame(99, $reader->getRecordCount());
    }

    public function testReadAllRecords(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        // testfile has 6 empty Lines and a header
        $this->assertCount(93, $reader->readAllRecords());
    }

    public function testReadRecordAtIndex(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $record = $reader->readRecordAtIndex(25);
        $this->assertCount(37, $record);
        $this->assertSame($this->expectedIndexRow, $record);
    }

    public function testReadRecordAtIndexThrowException(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        try {
            $records = $reader->readRecordAtIndex(105);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(OutOfRangeException::class, $exception);
            $this->assertSame(
                '$lineIndex is greater than the row count. File contains 99 records.',
                $exception->getMessage()
            );
        }
        try {
            $records = $reader->readRecordAtIndex(-1);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(OutOfRangeException::class, $exception);
            $this->assertSame(
                '$lineIndex must be a non negativ Integer.',
                $exception->getMessage()
            );
        }
    }

    public function testReadRecordsOfRange(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $records = $reader->readRecordsOfRange(10, 20);
        $this->assertCount(10, $records);
        foreach ($records as $index => $record) {
            $this->assertSame($this->expectedValuesFromRange[$index][0], $record[0]);
        }
    }

    public function testReadRecordsRangeThrowException(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        try {
            $records = $reader->readRecordsOfRange(10, 200);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(OutOfRangeException::class, $exception);
            $this->assertSame(
                '$rowIndexStop is greater than the row count. File contains 99 records.',
                $exception->getMessage()
            );
        }
        try {
            $records = $reader->readRecordsOfRange(-10, 20);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(OutOfRangeException::class, $exception);
            $this->assertSame('$rowIndexStart must be a non negativ Integer.', $exception->getMessage());
        }
    }

    /**
     * @testdox Tests the Header from a tab separated file
     * @covers ::getHeader
     * @throws CsvException
     */
    public function testGetHeaderFromTxtFile(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.txt');
        $reader->getStrategy()->setDelimiter("\t");
        $this->assertSame(
            $this->expectedHeader,
            $reader->getHeader()
        );
    }

    /**
     * @testdox Tests the record from a tab separated file
     * @covers ::readRecord
     * @throws CsvException
     */
    public function testGetRecordTxtTabFile(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.txt');
        $reader->getStrategy()->setDelimiter("\t");
        $readRecord = $reader->readRecord();
        $this->assertSame(
            $this->expectedFirstRecord,
            $readRecord
        );
    }

    /**
     * @testdox Test the field counter
     * @covers ::getFieldCount
     * @throws CsvException
     */
    public function testGetFieldCount(): void
    {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $this->assertSame(count($this->expectedFirstRecord), $reader->getFieldCount());
    }
}
