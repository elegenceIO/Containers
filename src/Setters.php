<?php
namespace ElegenceIO\Containers;

use Closure;
use Exception;
use InvalidArgumentException;

trait Setters
{

    public function lockTo(string $abstract,string $interface):self
    {
        if(!\interface_exists($interface))
        {
            throw new InvalidArgumentException("Failed to load {$abstract} {$interface} could not be found");
        }
    
        $this->locks[$abstract] = $interface;

        return $this;
    }

    public function alias(string $alias, string $abstract)
    {
        if($alias === $abstract)
        {
            throw new Exception("Cannot use the same name for an alias");
        }


        $this->alias[$alias] = $abstract;
        return $this;
    }
}