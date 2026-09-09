<?php
namespace ElegenceIO\Containers\Components;

use ElegenceIO\Support\Types\Reflection as TypesReflection;
use Exception;
use InvalidArgumentException;
use Reflection;
use ReflectionClass;

trait Resolver
{

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

    protected function resolveInterfaces(mixed $abstract)
    {
        // Obtain the Key Values of the  $abstract class;
        $keys = array_keys($this->bindings);
        if(in_array($abstract,$keys,true))
        {
            if(interface_exists($abstract) && !$this->bindings[$abstract] instanceof $abstract)
            {
                throw new Exception("Cannot Resolve Interface not implmented");
            }
        }
    }

    protected function resolveOverrides(string $abstract):void
    {
        if(array_key_exists($abstract,$this->overrides))
        {
            echo "Cannpt write to this file.";
        }
    }


    protected function autowire(string $abstract, mixed $callback=null)
    {
        $accepted = ["string","array","NULL"];

        if(!in_array(getType($callback),$accepted))
        {
            throw new Exception("Invaid Type");
        }

        if(is_array($callback))
        {
            if(!\in_array("method",array_keys($callback)))
            {
                echo "No key Method found";
            }

            $interfaces = $callback["interface"];
            $reflection = new ReflectionClass($abstract);

        
            if(isset($interfaces))
            {
                if(\is_array($interfaces))
                {
                    foreach($interfaces as $interface)
                    {
                        if(!$this->autowire->implements($reflection,$interface))
                        {
                            throw new Exception("must Implement an interface");
                        }
                    }
                }
                else
                {
                    if(!$this->autowire->implements($reflection,$interfaces))
                    {
                        throw new Exception("must Implement an interface");
                    }
                }
                exit();
            }
                
            $method = $callback["method"];
        }
        else
        {
            $method = $callback;
        }
        $wire = $this->autowire->register($abstract);
        return (\is_null($callback)) ? $wire : $this->autowire->call($wire, $method);
    }
}