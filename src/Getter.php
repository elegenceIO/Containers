<?php
namespace ElegenceIO\Containers;

use ElegenceIO\Support\Types\Reflection as TypesReflection;
use Exception;
use InvalidArgumentException;
use Reflection;
use ReflectionClass;

trait Getter
{
    public function resolveLocks(string $abstract,mixed $resolved)
    {
        $interface = $this->locks[$abstract] ?? null;
        if(\is_null($interface))
        {
            return;
        }

        if(!($resolved instanceof $interface))
        {
            $actual = \is_object($resolved) ? \get_class($resolved) : \gettype($resolved);

           
            throw new InvalidArgumentException(
                "Service [{$abstract}] must resolve to an instance of [{$interface}], " .
                "got [{$actual}]."
            );


        }
    }

    public function resolveAlias(string $abstract):string
    {
        $seen = [];
        while(isset($this->alias[$abstract]))
            {
                if(isset($seen[$abstract]))
                {
                    throw new InvalidArgumentException("Alias cannot be mapped already Loaded");
                }
        
            $seen[$abstract] = true;
            $abstract = $this->alias[$abstract];
        }

    
        return $abstract;
    }
}