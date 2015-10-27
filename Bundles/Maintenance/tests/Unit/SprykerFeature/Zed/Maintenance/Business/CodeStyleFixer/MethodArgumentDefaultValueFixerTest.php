<?php

namespace Unit\SprykerFeature\Zed\Maintenance\Business\CodeStyleFixer;

use SprykerFeature\Zed\Maintenance\Business\CodeStyleFixer\MethodArgumentDefaultValueFixer;

/**
 * @group SprykerFeature
 * @group Zed
 * @group Maintenance
 * @group CodeStyleFixer
 */
class MethodArgumentDefaultValueFixerTest extends \PHPUnit_Framework_TestCase
{

    const FIXER_NAME = 'MethodArgumentDefaultValueFixer';

    /**
     * @var MethodArgumentDefaultValueFixer
     */
    protected $fixer;

    protected function setUp()
    {
        parent::setUp();
        $this->fixer = new MethodArgumentDefaultValueFixer();
    }

    /**
     * @dataProvider provideFixCases
     *
     * @param string $expected
     * @param string $input
     *
     * @return void
     */
    public function testFix($expected, $input = null)
    {
        $fileInfo = new \SplFileInfo(__FILE__);
        $this->assertSame($expected, $this->fixer->fix($fileInfo, $input));
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        $fixturePath = __DIR__ . '/Fixtures/' . self::FIXER_NAME . '/';

        return [
            [
                file_get_contents($fixturePath . 'Expected/TestClass1Expected.php'),
                file_get_contents($fixturePath . 'Input/TestClass1Input.php'),
            ],
            [
                file_get_contents($fixturePath . 'Expected/TestClass2Expected.php'),
                file_get_contents($fixturePath . 'Input/TestClass2Input.php'),
            ],
        ];
    }

}
