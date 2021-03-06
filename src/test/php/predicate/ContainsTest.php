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
 * Tests for stubbles\predicate\Contains.
 *
 * @group  predicate
 * @since  4.0.0
 */
class ContainsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function constructionWithObjectThrowsIllegalArgumentException()
    {
        new Contains(new \stdClass());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function constructionWithNullThrowsIllegalArgumentException()
    {
        new Contains(null);
    }

    /**
     * returns tuples which evaluate to true
     *
     * @return  array
     */
    public function tuplesEvaluatingToTrue()
    {
        return [[true, true],
                [false, false],
                [5, 5],
                [5, 55],
                [5, 25],
                [5, 'foo5'],
                [5, 'fo5o'],
                ['foo', 'foobar'],
                ['foo', 'foo']
        ];
    }

    /**
     * @param  scalar  $contained
     * @param  mixed   $value
     * @test
     * @dataProvider  tuplesEvaluatingToTrue
     */
    public function evaluatesToTrue($contained, $value)
    {
        $contains = new Contains($contained);
        $this->assertTrue($contains($value));
    }

    /**
     * returns tuples which evaluate to false
     *
     * @return  array
     */
    public function tuplesEvaluatingToFalse()
    {
        return [[true, false],
                [false, true],
                [false, new \stdClass()],
                [false, null],
                [5, 'foo'],
                [5, 6],
                [true, 5],
                [false, 0],
                [true, 'foo'],
                ['foo', 'bar']
        ];
    }

    /**
     * @param  scalar  $contained
     * @param  mixed   $value
     * @test
     * @dataProvider  tuplesEvaluatingToFalse
     */
    public function evaluatesToFalse($contained, $value)
    {
        $contains = new Contains($contained);
        $this->assertFalse($contains($value));
    }
}
