<?php

namespace ElegenceIO\Containers;

use Closure;
use Exception;
use ElegenceIO\Containers\Components\Resolver;
use ElegenceIO\Containers\Components\Register;
use ElegenceIO\Contracts\Containers\Makable;
use ElegenceIO\Foundation\Compiler\AutoWirer;
use ReflectionClass;

class Container implements Makable
{
    protected array $instances = [];
    protected array $bindings = [];
    protected array $locks = [];
    protected array $alias = [];
    protected array $overrides = [];
    protected ?AutoWirer $autowire = null;
    protected array $make = [];
    protected array $tag = [];
    use Resolver;
    use Register;

    public function __construct()
    {
        $this->autowire = new AutoWirer($this);
    }



    /**
     * public @method bind()
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     * @description Registers a container with the Container Registry.
     * @description Support abstract types of class string and interfaces, concrete accepts class objects and strings
     * @description rejects closures use singleton methhod for factory instances
     */
    public function bind(string $abstract, mixed $concrete = null):void
    {
        
        $this->make[$abstract] = true;
        $this->rejectClosure($abstract,$concrete);
        match (true) {
        ($concrete === null) => $this->setClass($abstract),
        // ($concrete instanceof \Closure) => throw new InvalidArgumentException("Closures are not permitted in bind() for [$abstract]. Use singleton() instead."),
        (interface_exists($abstract) && (!is_null($concrete))) => $this->setInterface($abstract, $concrete),
        // (\is_string($concrete) && !\interface_exists($concrete)) => $this->alias($abstract, $concrete),
        (\is_object($concrete)) => $this->setObject($abstract,$concrete),
        default => $this->setDefault($abstract, $concrete),
    
};
        // return new Bond($this, $abstract);
    }

    public function singleton(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = function () use ($concrete, $abstract) {
            return $this->instances[$abstract] ??= $concrete($this);
        };
    }

    /**
     * public @method has()
     * @return Bool
     * @description Validates if a Container is registered
     * @description Manadatory for psr-11 compliance.
     */
    public function has(string $abstract): bool
    {
        return \array_key_exists($abstract, $this->bindings) ? true : false;
    }

    /**
     * public @method get()
     * @return mixed
     * @description Returns Registered container
     * @description Manadatory for psr-11 compliance.
     */
    public function get(string $abstract): mixed
    {
        if (!isset($this->make[$abstract]) || $this->make[$abstract] !== true) {
            throw new Exception("Cannot get a container without make() method");
        }

        // Set the Binding;
        $binding = $this->bindings[$abstract];
        // Resolv Binding
        return is_callable($binding) ? $binding($this) : $binding;
    }

    public function make(string $abstract, mixed $callback=null)
    {
        if ($this->has($abstract)) {
             $abstract = $this->resolveAlias($abstract);
             $this->resolveInterfaces($abstract);
            return $this->get($abstract);
        }
        else{
        return $this->autowire($abstract, $callback);
        }
    }

}
