<?php
declare (strict_types = 1);

namespace vansari\csv;

use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase {
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
        '677509', 'Drs.', 'Lois', 'H', 'Walker', 'F', 'lois.walker@hotmail.com', 'Donald Walker',
        'Helen Walker', 'Lewis', '3/29/1981', '02:31:49 AM', '36.36', '60', '11/24/2003', 'Q4', 'H2',
        '2003', '11', 'November', 'Nov', '24', 'Monday', 'Mon', '13.68', '168251', '21%', '467-99-4677',
        '303-572-8492', 'Denver', 'Denver', 'Denver', 'CO', '80224', 'West', 'lhwalker',
        'DCa}.T}X:v?NP',
    ];

    /**
     * @testdox Tests if we can retrieve the Header if hasHeader returns true otherwise null
     */
    public function testGetHeader(): void {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $header = $reader->getHeader();
        $this->assertSame(
            $this->expectedHeader,
            $header
        );
    }

    public function testGetHeaderFailed(): void {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        // Header did not match if Strategy is set to skip 1 line
        $reader->getStrategy()->setSkipLeadingLinesCount(1);
        $this->assertSame(1, $reader->getStrategy()->getSkipLeadingLinesCount());
        $this->assertNotSame($this->expectedHeader, $reader->getHeader());
    }

    public function testReadRecord(): void {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $this->assertSame($this->expectedFirstRecord, $reader->readRecord());
    }

    public function testGetRecordCount(): void {
        $reader = new Reader(__DIR__ . '/testfile.csv');
        $this->assertSame(100, $reader->getRecordCount());
    }
}
