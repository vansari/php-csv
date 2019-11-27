<?php
declare (strict_types=1);

namespace csv\util;

use PHPUnit\Framework\TestCase;

/**
 * Class HeaderNormalizerTest
 * @package util
 * @coversDefaultClass \csv\util\HeaderNormalizer
 */
class HeaderNormalizerTest extends TestCase
{
    /**
     * @return array
     */
    public function getItemsToNormalize(): array
    {
        return [
            [
                '1-Header.test',
                'one_Header_test',
            ],
            [
                '_Header_!"§$Test=)(/',
                'Header_Test',
            ],
            [
                'heAdEr 1. Test (Beta)',
                'heAdEr_one_Test_Beta',
            ],
        ];
    }

    /**
     * @covers ::normalize
     */
    public function testNormalize(): void
    {
        $stringToNormalize = 'Rolling-Street 7';
        $expected = 'Rolling_Street_7';

        $this->assertSame($expected, (new HeaderNormalizer())->normalize($stringToNormalize));
    }

    /**
     * @covers ::normalize
     * @covers ::pushPatternReplacement
     * @dataProvider getItemsToNormalize
     */
    public function testNormalizeWithAddCustomPatterns(string $source, string $expected): void
    {
        $normalizer = new HeaderNormalizer();
        $normalizer->pushPatternReplacement(['/1/',], ['one',]);
        $this->assertSame($expected, $normalizer->normalize($source));
    }

    /**
     * @covers ::normalize
     * @covers ::unshiftPatternReplacement
     * @testdox test to replace some special chars with addPatterns and setPatterns
     */
    public function testNormalizeWithSetOwnPatterns(): void
    {
        $stringToNormalize = 'Foo!Bar-3+one';
        $expectedString = 'Foo_a_Bar_three_one';
        $patterns = ['/!/', '/3/'];
        $replacement = ['_a_', 'three'];
        // Be aware that pushPatternReplacement will merge the custom patterns to end of array and special chars
        // were replaced before with default pattern/replacement so that this test will fail
        $this->assertNotSame(
            $expectedString,
            (new HeaderNormalizer())->pushPatternReplacement($patterns, $replacement)->normalize($stringToNormalize)
        );

        // Instead of using pushPatternReplacement you can use unshiftPatternReplacement
        $this->assertSame(
            $expectedString,
            (new HeaderNormalizer())
                ->unshiftPatternReplacement($patterns, $replacement)
                ->normalize($stringToNormalize)
        );
    }

    /**
     * @covers ::normalizeHeader
     */
    public function testNormalizeHeader(): void
    {
        $headerToNormalize = [
            'Amount in %',
            'Amount p.a.',
            'descript. of Products',
            'Storage Time (Years)',
        ];

        $expectedHeader = [
            'Amount_in_p',
            'Amount_p_a',
            'descript_of_Products',
            'Storage_Time_Years',
        ];

        $this->assertSame(
            $expectedHeader,
            (new HeaderNormalizer())
                ->unshiftPatternReplacement(['/%/'], ['p'])
                ->normalizeHeader($headerToNormalize)
        );
    }

    public function testNormalizeWithNewSetOfPatternReplacement(): void {
        $stringToNormalize = 'Foo!Bar-3+one';
        $expectedString = 'Foo_a_Bar-three+one';
        $patterns = ['/!/', '/3/'];
        $replacement = ['_a_', 'three'];
        // Be aware that pushPatternReplacement will merge the custom patterns to end of array and special chars
        // were replaced before with default pattern/replacement so that this test will fail
        $this->assertSame(
            $expectedString,
            (new HeaderNormalizer())->setPatternReplacement($patterns, $replacement)->normalize($stringToNormalize)
        );
    }
}
