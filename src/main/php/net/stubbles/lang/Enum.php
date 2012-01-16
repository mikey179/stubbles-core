<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\exception\RuntimeException;
/**
 * Base class for enums.
´´
 */
abstract class Enum extends BaseObject
{
    /**
     * name of the enum
     *
     * @type  string
     */
    protected $name;
    /**
     * value of enum
     *
     * @type  int
     */
    protected $value;

    /**
     * constructor
     *
     * @param  string  $name   enum name
     * @param  mixed   $value  enum value
     */
    protected function __construct($name, $value = null)
    {
        $this->name  = $name;
        $this->value = ((null !== $value) ? ($value) : ($name));
    }

    /**
     * forbid cloning of enums
     *
     * @throws  stubRuntimeException
     */
    public final function __clone()
    {
        throw new RuntimeException('Cloning of enums is not allowed.');
    }

    /**
     * returns the name of the enum
     *
     * @return  string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * returns the value of the enum
     *
     * @return  mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * returns the enum instance of given class identified by its name
     *
     * @param   string  $name
     * @return  Enum
     * @throws  IllegalArgumentException
     */
    public static function forName($name)
    {
        $enum = new \ReflectionClass(get_called_class());
        try {
            return $enum->getStaticPropertyValue($name);
        } catch (\ReflectionException $re) {
            throw new IllegalArgumentException($re->getMessage());
        }
    }

    /**
     * returns the enum instance of given class identified by its value
     *
     * @param   string  $value
     * @return  Enum
     * @throws  IllegalArgumentException
     */
    public static function forValue($value)
    {
        $enumClass = new \ReflectionClass(get_called_class());
        try {
            foreach ($enumClass->getStaticProperties() as $instance) {
                if ($instance->value() === $value) {
                    return $instance;
                }
            }
        } catch (\ReflectionException $re) {
            throw new IllegalArgumentException($re->getMessage());
        }

        throw new IllegalArgumentException('Enum ' . $enumClass->getName() . ' for value ' . $value . ' does not exist.');
    }

    /**
     * returns a list of all instances for given enum
     *
     * @return  Enum[]
     */
    public static function instances()
    {
        $enum = new \ReflectionClass(get_called_class());
        return array_values($enum->getStaticProperties());
    }

    /**
     * returns a list of enum names for given enum
     *
     * @return  string[]
     */
    public static function namesOf()
    {
        $enum = new \ReflectionClass(get_called_class());
        return array_keys($enum->getStaticProperties());
    }

    /**
     * returns a list of values for given enum
     *
     * @return  mixed[]
     */
    public static function valuesOf()
    {
        $enum   = new \ReflectionClass(get_called_class());
        $values = array();
        foreach ($enum->getStaticProperties() as $name => $instance) {
            $values[$name] = $instance->value;
        }

        return $values;
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     * @XmlIgnore
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($compare->getClassName() === $this->getClassName() && $compare->name() === $this->name);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @return  string
     * @XmlIgnore
     */
    public function __toString()
    {
        $string  = $this->getClassName() . " {\n";
        $string .= '    ' . $this->name . "\n";
        $string .= '    ' . $this->value . "\n";
        $string .= "}\n";
        return $string;
    }
}
?>