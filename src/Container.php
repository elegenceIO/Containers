<?php
namespace ElegenceIO\Containers;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;
use ElegenceIO\Containers\Components\Resolver;
use ElegenceIO\Containers\Components\Writer;

class Container implements ContainerInterface
{
    use Resolver;
    use Writer;
    protected array $instances = [];
    protected array $bindings = [];
    protected array $locks = [];
    protected array $alias = [];
    

     public function map(string $abstract,mixed $concrete)
    {

        match(true)
        {
            (\class_exists($abstract) && empty($concrete)) => $this->bindings[$abstract] = new $abstract(),
            (\is_callable($concrete)) => $this->bindings[$abstract] = $concrete,
            default => $this->bindings[$abstract] = $concrete,
        };
        
        return new Registration($this,$abstract);

    }

    public function bind(string $abstract,callable $concrete):Registration
    {
        $this->bindings[$abstract] = $concrete;

        return new Registration($this,$abstract);
    }

    public function singleton(string $abstract, callable $concrete):Registration
    {
        $this->bindings[$abstract] = function() use ($concrete,$abstract)
        {
            return $this->instances[$abstract] ??= $concrete($this);
        };
        return new Registration($this,$abstract);
    }

    public function has(string $abstract):bool
    {
        return isset($this->bindings[$abstract]);
    }

    public function get(string $abstract)
    {
        return $this->make($abstract);
    }

    public function make(string $abstract)
    {
        $abstract = $this->resolveAlias($abstract);
        if (!$this->has($abstract)) {
        throw new class(
            "Service [$abstract] is not bound in the container."
        ) extends InvalidArgumentException
          implements NotFoundExceptionInterface {};
    }

        $binding = $this->bindings[$abstract];
        $resolved = is_callable($binding) ? $binding($this) : $binding;

        $this->resolveLocks($abstract,$resolved);

        return $resolved;

    }


    // create Private function to match with types.

    // Convert to Alais




    
}