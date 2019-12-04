<?php
declare (strict_types=1);

namespace csv;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StrategyTest extends TestCase
{

    /**
     * @covers Strategy::setDelimiter
     */
    public function testSetDelimiter(): void
    {
        $validDelimiter = [
            ",", ";", "\t", "|",
        ];

        $invalidDelimiter = [
            '\t', '\n', "\n",
        ];

        foreach ($validDelimiter as $delimiter) {
            $strategy = Strategy::createStrategy()->setDelimiter($delimiter);
            $this->assertSame($delimiter, $strategy->getDelimiter());
        }

        foreach ($invalidDelimiter as $delimiter) {
            try {
                $strategy = Strategy::createStrategy()->setDelimiter($delimiter);
                $this->assertNotSame($delimiter, $strategy->getDelimiter(), print_r($delimiter, true));
            } catch (InvalidArgumentException $exception) {
                $this->assertSame('Delimiter must be only one character and not a new line.', $exception->getMessage());
            }
        }
    }

    /**
     * @covers Strategy::createStrategy
     */
    public function testCreateStrategy(): void
    {

    }
}
