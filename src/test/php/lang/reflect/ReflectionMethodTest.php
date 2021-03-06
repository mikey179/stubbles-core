<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect;
use \stubbles\lang;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * class to be used for the test
 */
class TestMethodCollection
{
    /**
     * does not return anything
     */
    public function methodWithoutParams()
    {
        // intentionally empty
    }

    /**
     * returns a scalar value
     *
     * @param   string  $param1
     * @param   mixed   $param2
     * @return  string
     */
    public function methodWithParams($param1, $param2)
    {
        return 'foo';
    }
}
/**
 * another class to be used for the test
 */
class TestMethodCollection2 extends TestMethodCollection
{
    /**
     * returns a class instance
     *
     * @param   int       $param3
     * @return  stubbles\test\lang\reflect\TestWithMethodsAndProperties
     * @SomeAnnotation
     * @AnotherAnnotation
     * @Foo('bar')
     * @Foo('baz')
     * @SomeParam{param3}
     */
    public function methodWithParams2($param3)
    {
        return new stubbles\test\lang\reflect\TestWithMethodsAndProperties();
    }

    public function methodWithoutDocblock()
    {
        // intentionally empty
    }

    /**
     * @return  void
     */
    public function methodWithReturnTypeVoid()
    {
        // intentionally empty
    }
}
/**
 * Test for stubbles\lang\reflect\ReflectionMethod.
 *
 * @group  lang
 * @group  lang_reflect
 */
class ReflectionMethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to test
     *
     * @type  ReflectionMethod
     */
    protected $refMethod1;
    /**
     * instance 2 to test
     *
     * @type  ReflectionMethod
     */
    protected $refMethod2;
    /**
     * instance 3 to test
     *
     * @type  ReflectionMethod
     */
    protected $refMethod3;
    /**
     * instance 4 to test
     *
     * @type  ReflectionMethod
     */
    protected $refMethod4;
    /**
     * instance 5 to test
     *
     * @type  ReflectionMethod
     */
    protected $refMethod5;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->refMethod1 = lang\reflect('stubbles\lang\\reflect\TestMethodCollection', 'methodWithoutParams');
        $this->refMethod2 = lang\reflect('stubbles\lang\\reflect\TestMethodCollection', 'methodWithParams');
        $testMethodCollection2 = new \stubbles\lang\reflect\TestMethodCollection2();
        $this->refMethod3 = lang\reflect($testMethodCollection2, 'methodWithoutParams');
        $this->refMethod4 = lang\reflect($testMethodCollection2, 'methodWithParams');
        $this->refMethod5 = lang\reflect($testMethodCollection2, 'methodWithParams2');
    }

    /**
     * @test
     */
    public function isEqualToSameInstance()
    {
        $this->assertTrue($this->refMethod1->equals($this->refMethod1));
        $this->assertTrue($this->refMethod2->equals($this->refMethod2));
        $this->assertTrue($this->refMethod3->equals($this->refMethod3));
        $this->assertTrue($this->refMethod4->equals($this->refMethod4));
        $this->assertTrue($this->refMethod5->equals($this->refMethod5));
    }

    /**
     * @test
     */
    public function isEqualToOtherInstanceOfSameMethod()
    {
        $refMethod = lang\reflect('stubbles\lang\\reflect\TestMethodCollection', 'methodWithoutParams');
        $this->assertTrue($this->refMethod1->equals($refMethod));
        $this->assertTrue($refMethod->equals($this->refMethod1));
    }

    /**
     * @test
     */
    public function isNotEqualToAnyOtherInstance()
    {
        $this->assertFalse($this->refMethod1->equals($this->refMethod2));
        $this->assertFalse($this->refMethod1->equals($this->refMethod3));
        $this->assertFalse($this->refMethod1->equals($this->refMethod4));
        $this->assertFalse($this->refMethod1->equals($this->refMethod5));
        $this->assertFalse($this->refMethod2->equals($this->refMethod1));
        $this->assertFalse($this->refMethod2->equals($this->refMethod3));
        $this->assertFalse($this->refMethod2->equals($this->refMethod4));
        $this->assertFalse($this->refMethod2->equals($this->refMethod5));
        $this->assertFalse($this->refMethod3->equals($this->refMethod1));
        $this->assertFalse($this->refMethod3->equals($this->refMethod2));
        $this->assertFalse($this->refMethod3->equals($this->refMethod4));
        $this->assertFalse($this->refMethod3->equals($this->refMethod5));
        $this->assertFalse($this->refMethod4->equals($this->refMethod1));
        $this->assertFalse($this->refMethod4->equals($this->refMethod2));
        $this->assertFalse($this->refMethod4->equals($this->refMethod3));
        $this->assertFalse($this->refMethod4->equals($this->refMethod5));
        $this->assertFalse($this->refMethod5->equals($this->refMethod1));
        $this->assertFalse($this->refMethod5->equals($this->refMethod2));
        $this->assertFalse($this->refMethod5->equals($this->refMethod3));
        $this->assertFalse($this->refMethod5->equals($this->refMethod4));
    }

    /**
     * @test
     */
    public function isNotEqualToAnyOtherType()
    {
        $this->assertFalse($this->refMethod1->equals('foo'));
    }

    /**
     * @test
     */
    public function stringRepresentationContainsInformationAboutReflectedMethod()
    {
        $this->assertEquals("stubbles\lang\\reflect\ReflectionMethod[stubbles\lang\\reflect\TestMethodCollection::methodWithoutParams()] {\n}\n",
                            (string) $this->refMethod1
        );
        $this->assertEquals("stubbles\lang\\reflect\ReflectionMethod[stubbles\lang\\reflect\TestMethodCollection::methodWithParams()] {\n}\n",
                            (string) $this->refMethod2
        );
        $this->assertEquals("stubbles\lang\\reflect\ReflectionMethod[stubbles\lang\\reflect\TestMethodCollection2::methodWithoutParams()] {\n}\n",
                            (string) $this->refMethod3
        );
        $this->assertEquals("stubbles\lang\\reflect\ReflectionMethod[stubbles\lang\\reflect\TestMethodCollection2::methodWithParams()] {\n}\n",
                            (string) $this->refMethod4
        );
        $this->assertEquals("stubbles\lang\\reflect\ReflectionMethod[stubbles\lang\\reflect\TestMethodCollection2::methodWithParams2()] {\n}\n",
                            (string) $this->refMethod5
        );
    }

    /**
     * @test
     */
    public function getDeclaringClassReturnsReflectionClassForDeclaringClass()
    {
        $refClass = $this->refMethod1->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\TestMethodCollection',
                            $refClass->getName()
        );

        $refClass = $this->refMethod2->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\TestMethodCollection',
                            $refClass->getName()
        );

        $refClass = $this->refMethod3->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\TestMethodCollection',
                            $refClass->getName()
        );

        $refClass = $this->refMethod4->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\TestMethodCollection',
                            $refClass->getName()
        );

        $refClass = $this->refMethod5->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\TestMethodCollection2',
                            $refClass->getName()
        );
    }

    /**
     * @test
     */
    public function getParametersReturnsEmptyListIfMethodDoesNotHaveParameters()
    {
        $this->assertEquals([], $this->refMethod1->getParameters());
        $this->assertEquals([], $this->refMethod3->getParameters());
    }

    /**
     * @test
     */
    public function getParametersReturnsListOfReflectionParameter()
    {
        $refParameters = $this->refMethod2->getParameters();
        $this->assertEquals(2, count($refParameters));
        foreach ($refParameters as $refParameter) {
            $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionParameter',
                                    $refParameter
            );
        }

        $refParameters = $this->refMethod4->getParameters();
        $this->assertEquals(2, count($refParameters));
        foreach ($refParameters as $refParameter) {
            $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionParameter',
                                    $refParameter
            );
        }

        $refParameters = $this->refMethod5->getParameters();
        $this->assertEquals(1, count($refParameters));
        foreach ($refParameters as $refParameter) {
            $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionParameter',
                                    $refParameter
            );
        }
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsNullIfNoDocblockDefined()
    {
        $refMethod = lang\reflect('stubbles\lang\\reflect\TestMethodCollection2', 'methodWithoutParams');
        $this->assertNull($refMethod->getReturnType());
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsNullIfReturnTypeIsVoid()
    {
        $refMethod = lang\reflect('stubbles\lang\\reflect\TestMethodCollection2', 'methodWithReturnTypeVoid');
        $this->assertNull($refMethod->getReturnType());
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsNullIfNoReturnTypeInDocblock()
    {
        $this->assertNull($this->refMethod1->getReturnType());
        $this->assertNull($this->refMethod3->getReturnType());
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsPrimitiveIfReturnTypeIsPrimitive()
    {
        $this->assertSame(ReflectionPrimitive::$STRING, $this->refMethod2->getReturnType());
        $this->assertSame(ReflectionPrimitive::$STRING, $this->refMethod4->getReturnType());
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsReflectionClassIfReturnTypeIsObject()
    {
        $refClass = $this->refMethod5->getReturnType();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\test\lang\\reflect\TestWithMethodsAndProperties',
                            $refClass->getName()
        );
    }

    /**
     * @test
     */
    public function instantiationWithReflectionClass()
    {
        $refClass1  = lang\reflect('stubbles\lang\\reflect\TestMethodCollection');
        $refClass2  = lang\reflect('stubbles\lang\\reflect\TestMethodCollection2');
        $refMethod1 = lang\reflect($refClass1, 'methodWithoutParams');
        $this->assertSame($refClass1, $refMethod1->getDeclaringClass());
        $refMethod2 = lang\reflect($refClass1, 'methodWithParams');
        $this->assertSame($refClass1, $refMethod2->getDeclaringClass());
        $refMethod3 = lang\reflect($refClass2, 'methodWithoutParams');
        $this->assertEquals('stubbles\lang\\reflect\TestMethodCollection',
                            $refMethod3->getDeclaringClass()->getName()
        );
        $refMethod4 = lang\reflect($refClass2, 'methodWithParams');
        $this->assertEquals('stubbles\lang\\reflect\TestMethodCollection',
                            $refMethod4->getDeclaringClass()->getName()
        );
        $refMethod5 = lang\reflect($refClass2, 'methodWithParams2');
        $this->assertSame($refClass2, $refMethod5->getDeclaringClass());
    }

    /**
     * @test
     */
    public function getExtensionReturnsNullIfMEthodIsNotPartOfAnExtension()
    {
        $this->assertNull($this->refMethod1->getExtension());
    }

    /**
     * @test
     */
    public function getExtensionReturnsReflectionExtensionIfClassIsPartOfAnExtension()
    {
        $refClass = lang\reflect('\DateTime', '__construct');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionExtension',
                                $refClass->getExtension()
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function hasAnnotationReturnsFalseIfNoAnnotationIsPresent()
    {
        $this->assertFalse($this->refMethod1->hasAnnotation('Other'));
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function getAnnotationReturnsAnnotation()
    {
        $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
                                $this->refMethod5->annotation('SomeAnnotation')
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function annotationsReturnsListOfAllAnnotation()
    {
        $this->assertEquals(
                [new Annotation('SomeAnnotation', 'stubbles\lang\reflect\TestMethodCollection2::methodWithParams2()'),
                 new Annotation('AnotherAnnotation', 'stubbles\lang\reflect\TestMethodCollection2::methodWithParams2()'),
                 new Annotation('Foo', 'stubbles\lang\reflect\TestMethodCollection2::methodWithParams2()', ['__value' => 'bar']),
                 new Annotation('Foo', 'stubbles\lang\reflect\TestMethodCollection2::methodWithParams2()', ['__value' => 'baz'])
                ],
                $this->refMethod5->annotations()->all()
        );
    }
}
