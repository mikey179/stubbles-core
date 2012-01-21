<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\exception;
/**
 * Tests for net\stubbles\lang\exception\IllegalArgumentException.
 *
 * @group  lang
 * @group  lang_exception
 */
class IllegalArgumentExceptionTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function instanceCanBeThrown()
    {
        throw new IllegalArgumentException('error');
    }
}
?>