<?php
namespace ElegenceIO\Containers\Components;

use Closure;
use Exception;
use InvalidArgumentException;

trait Register
{

    
    
    /**
     * Sets an interface binding for an abstract type
     */
    protected function setInterface(string $abstract,string|object $concrete):mixed
    {
        return is_string($concrete) && class_exists($concrete)
         ? $this->bindings[$abstract] = new $concrete() 
         : $this->bindings[$abstract] = $concrete;
    }
    
    /**
     * @param string $abstract
     * @param Closure $concrete
     * @throws Exception
     * @return callable
     */
    protected function setObject(string $abstract,object $concrete):object
    {
        // Refuses if string is an existing classname 
      if (class_exists($abstract) && !($concrete instanceof $abstract)) {
        throw new Exception(
            "Cannot bind [$abstract] to an instance of [" . get_class($concrete) . "] — " .
            "the concrete must be an instance of [$abstract] or implement it."
        );
    }
        return $this->bindings[$abstract] = $concrete;

    }

    /**
     * Sets a default binding for an abstract type.
     * @param string $abstract The abstract type to bind.
     * @param mixed $concrete The concrete implementation to bind.
     * @return mixed
     */
    protected function setDefault(string $abstract, mixed $concrete):mixed
    {
        return $this->bindings[$abstract] = $concrete;
        
    }

    private function rejectClosure(string $abstract,mixed $concrete)
    {
          if($concrete instanceof \Closure)
        {
            throw new Exception("$abstract cannot be instance of closure");
        }

    }

    protected function throwInvalidArgumentException(string $abstract, mixed $concrete):void
    {
        throw new InvalidArgumentException(
            "Cannot bind [$abstract] to an instance of [" . gettype($concrete) . "] — " .
            "the concrete must be an instance of [$abstract] or implement it."
        );
    }

    protected function setClass(string $abstract):mixed
    {
        if(!class_exists($abstract))
        {
            throw new InvalidArgumentException("Class {$abstract} does not exist.");
        }
    
        return $this->bindings[$abstract] = new $abstract();
    }


    protected function lockTo(mixed $abstract,string $interface):self
    {
        if(!\interface_exists($interface))
        {
            throw new InvalidArgumentException("Failed to load {$abstract} {$interface} could not be found");
        }
    
        $this->locks[$abstract] = $interface;

        return $this;
    }

    public function alias(string $alias, string $abstract):self|bool
    {
        if (class_exists($abstract) && is_object($abstract) && !($abstract instanceof $alias)) {
        throw new Exception(
            "Cannot bind [$abstract] to an instance of [" . get_class($abstract) . "] — " .
            "the concrete must be an instance of [$abstract] or implement it."
        );
        }

        if($alias === $abstract)
        {
            throw new Exception("Cannot use the same name for an alias");
        }

        $this->alias[$alias] = $abstract;
        return $this;
    }

    protected function preventOverride(string $abstract):self
    {
            $this->overrides[$abstract] = true;
            return $this;
    }

}