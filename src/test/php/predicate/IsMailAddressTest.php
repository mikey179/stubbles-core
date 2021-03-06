<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\predicate;
/**
 * Tests for stubbles\predicate\IsMailAddress.
 *
 * @group  predicate
 * @since  4.0.0
 */
class IsMailAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IsMailAddress
     */
    protected $isMailAddress;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->isMailAddress = new IsMailAddress();
    }

    /**
     * @return  array
     */
    public function validValues()
    {
        return [['example@example.org'],
                ['example.foo.bar@example.org']
        ];
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  validValues
     */
    public function validValueEvaluatesToTrue($value)
    {
        $this->assertTrue($this->isMailAddress->test($value));
    }

    /**
     * @return  array
     */
    public function invalidValues()
    {
        return [['space in@mailadre.ss'],
                ['fäö@mailadre.ss'],
                ['foo@bar@mailadre.ss'],
                ['foo&/4@mailadre.ss'],
                ['foo..bar@mailadre.ss'],
                [null],
                [''],
                ['xcdsfad'],
                ['foobar@thishost.willnever.exist'],
                ['.foo.bar@example.org'],
                ['example@example.org\n'],
                ['example@exa"mple.org'],
                ['example@example.org\nBcc: example@example.com']
        ];
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  invalidValues
     */
    public function invalidValueEvaluatesToFalse($value)
    {
        $this->assertFalse($this->isMailAddress->test($value));
    }

    /**
     * @test
     */
    public function validatesIndependendOfLowerOrUpperCase()
    {
        $this->assertTrue($this->isMailAddress->test('Example@example.ORG'));
        $this->assertTrue($this->isMailAddress->test('Example.Foo.Bar@EXAMPLE.org'));
    }
}
