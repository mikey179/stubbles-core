<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect;
use net\stubbles\lang\Enum;
/**
 * Type reference for primitives.
 */
class ReflectionPrimitive extends Enum implements ReflectionType
{
    /**
     * primitive of type string
     *
     * @type  ReflectionPrimitive
     */
    public static $STRING;
    /**
     * primitive of type int
     *
     * @type  ReflectionPrimitive
     */
    public static $INT;
    /**
     * primitive of type int, marked as integer
     *
     * @type  ReflectionPrimitive
     */
    public static $INTEGER;
    /**
     * primitive of type float
     *
     * @type  ReflectionPrimitive
     */
    public static $FLOAT;
    /**
     * primitive of type double, equal to float
     *
     * @type  ReflectionPrimitive
     */
    public static $DOUBLE;
    /**
     * primitive of type bool
     *
     * @type  ReflectionPrimitive
     */
    public static $BOOL;
    /**
     * primitive of type bool, marked as boolean
     *
     * @type  ReflectionPrimitive
     */
    public static $BOOLEAN;
    /**
     * primitive of type array
     *
     * @type  ReflectionPrimitive
     */
    public static $ARRAY;

    /**
     * static initializing
     */
    public static function __static()
    {
        self::$STRING  = new self('string', 'string');
        self::$INT     = new self('int', 'int');
        self::$INTEGER = new self('integer', 'int');
        self::$FLOAT   = new self('float', 'float');
        self::$DOUBLE  = new self('double', 'float');
        self::$BOOL    = new self('bool', 'bool');
        self::$BOOLEAN = new self('boolean', 'bool');
        self::$ARRAY   = new self('array', 'array');
    }

    /**
     * returns the enum instance of given class identified by its name
     *
     * @param   string  $name
     * @return  ReflectionPrimitive
     */
    public static function forName($name)
    {
        if (substr(strtolower($name), 0, 5) == 'array') {
            return parent::forName('ARRAY');
        }

        return parent::forName(strtoupper($name));
    }

    /**
     * returns the name of the type
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name();
    }

    /**
     * checks whether the type is an object
     *
     * @return  bool
     */
    public function isObject()
    {
        return false;
    }

    /**
     * checks whether the type is a primitive
     *
     * @return  bool
     */
    public function isPrimitive()
    {
        return true;
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($compare->value == $this->value);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @return  string
     */
    public function __toString()
    {
        return __CLASS__ . '[' . $this->value . "] {\n}\n";
    }
}
ReflectionPrimitive::__static();
?>