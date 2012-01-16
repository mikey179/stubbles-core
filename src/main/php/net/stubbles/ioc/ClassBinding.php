<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\reflect\BaseReflectionClass;
use net\stubbles\lang\reflect\ReflectionClass;
/**
 * Binding to bind an interface to an implementation.
 *
 * Please note that you can do a binding to a class or to an instance, or to an
 * injection provider, or to an injection provider class. These options are
 * mutually exclusive and have a predictive order:
 * 1. Instance
 * 2. Provider instance
 * 3. Provider class
 * 4. Concrete implementation class
 */
class ClassBinding extends BaseObject implements Binding
{
    /**
     * injector used by this binding
     *
     * @type  Injector
     */
    protected $injector      = null;
    /**
     * type for this binding
     *
     * @type  string
     */
    protected $type          = null;
    /**
     * class that implements this binding
     *
     * @type  ReflectionClass
     */
    protected $impl          = null;
    /**
     * Annotated with a name
     *
     * @type  string
     */
    protected $name          = null;
    /**
     * scope of the binding
     *
     * @type  BindingScope
     */
    protected $scope         = null;
    /**
     * instance this type is bound to
     *
     * @type  object
     */
    protected $instance      = null;
    /**
     * provider to use for this binding
     *
     * @type  InjectionProvider
     */
    protected $provider      = null;
    /**
     * provider class to use for this binding (will be created via injector)
     *
     * @type  string
     */
    protected $providerClass = null;
    /**
     * list of available binding scopes
     *
     * @type  BindingScopes
     */
    protected $scopes;

    /**
     * constructor
     *
     * @param  Injector       $injector
     * @param  string         $type
     * @param  BindingScopes  $scopes
     */
    public function __construct(Injector $injector, $type, BindingScopes $scopes)
    {
        $this->injector = $injector;
        $this->type     = $type;
        $this->impl     = $type;
        $this->scopes   = $scopes;
    }

    /**
     * set the concrete implementation
     *
     * @param   BaseReflectionClass|string  $impl
     * @return  ClassBinding
     * @throws  IllegalArgumentException
     */
    public function to($impl)
    {
        if (is_string($impl) === false && ($impl instanceof BaseReflectionClass) === false) {
            throw new IllegalArgumentException('$impl must be a string or an instance of net\\stubbles\\lang\\reflect\\BaseReflectionClass');
        }

        $this->impl = $impl;
        return $this;
    }

    /**
     * set the concrete instance
     *
     * This cannot be used in conjuction with the 'toProvider()' or
     * 'toProviderClass()' method.
     *
     * @param   object            $instance
     * @return  ClassBinding
     * @throws  IllegalArgumentException
     */
    public function toInstance($instance)
    {
        if (($instance instanceof $this->type) === false) {
            throw new IllegalArgumentException('Instance of ' . $this->type . ' expectected, ' . get_class($instance) . ' given.');
        }

        $this->instance = $instance;
        return $this;
    }

    /**
     * set the provider that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'toInstance()' or
     * 'toProviderClass()' method.
     *
     * @param   InjectionProvider  $provider
     * @return  ClassBinding
     */
    public function toProvider(InjectionProvider $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * set the provider class that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'toInstance()' or
     * 'toProvider()' method.
     *
     * @param   string|BaseReflectionClass  $providerClass
     * @return  ClassBinding
     */
    public function toProviderClass($providerClass)
    {
        $this->providerClass = (($providerClass instanceof BaseReflectionClass) ?
                                    ($providerClass->getName()) : ($providerClass));
        return $this;
    }

    /**
     * binds the class to the singleton scope
     *
     * @return  ClassBinding
     * @since   1.5.0
     */
    public function asSingleton()
    {
        $this->scope = $this->scopes->getSingletonScope();
        return $this;
    }

    /**
     * binds the class to the session scope
     *
     * @return  ClassBinding
     * @since   1.5.0
     */
    public function inSession()
    {
        $this->scope = $this->scopes->getSessionScope();
        return $this;
    }

    /**
     * set the scope
     *
     * @param   BindingScope  $scope
     * @return  ClassBinding
     */
    public function in(BindingScope $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Set the name of the injection
     *
     * @param   string            $name
     * @return  ClassBinding
     */
    public function named($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * returns the created instance
     *
     * @param   string  $type
     * @param   string  $name
     * @return  mixed
     * @throws  BindingException
     */
    public function getInstance($type, $name)
    {
        if (null !== $this->instance) {
            return $this->instance;
        }

        if (is_string($this->impl) === true) {
            $this->impl = new ReflectionClass($this->impl);
        }

        if (null === $this->scope) {
            if ($this->impl->hasAnnotation('Singleton') === true) {
                $this->scope = $this->scopes->getSingletonScope();
            }
        }

        if (null === $this->provider) {
            if (null != $this->providerClass) {
                $provider = $this->injector->getInstance($this->providerClass);
                if (($provider instanceof InjectionProvider) === false) {
                    throw new BindingException('Configured provider class ' . $this->providerClass . ' for type ' . $this->type . ' is not an instance of net\\stubbles\\ioc\\InjectionProvider.');
                }

                $this->provider = $provider;
            } else {
                $this->provider = new DefaultInjectionProvider($this->injector, $this->impl);
            }
        }

        if (null !== $this->scope) {
            return $this->scope->getInstance($this->impl, $this->provider);
        }

        return $this->provider->get($name);
    }

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey()
    {
        if (null === $this->name) {
            return $this->type;
        }

        return $this->type . '#' . $this->name;
    }
}
?>