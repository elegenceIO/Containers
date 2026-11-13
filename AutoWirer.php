<?php
namespace ElegenceIO\Containers;

use ElegenceIO\Contracts\Containers\Makable;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class AutoWirer
{
    public function __construct(private ?Makable $container = null)
    {
        $this->container = $container ?? null;
    }

    public function register(string $class):mixed
    {
        $reflect = new ReflectionClass($class);
        if(!$reflect->isInstantiable())
        {
            if($reflect->isAbstract())
                {
                    // Throw Error
                    throw new Exception("Cannot Register abstract class");
                }
            
            if($reflect->isInterface())
            {
                // throw Error
                throw new Exception("Cannot register interface");
            }
        }
        // Get the constructor

        $constructor = $reflect->getConstructor();

        if(\is_null($constructor))
        {
            return new $class();
        }
        
        $dependencies = $constructor->getParameters();
        return $reflect->newInstanceArgs($this->resolveParams($dependencies));
    }

    public function call(object $instance,string $method):mixed
    {
        $reflection = new ReflectionMethod($instance,$method);

        $dependencies = $this->resolveParams($reflection->getParameters());

        return $reflection->invokeArgs($instance,$dependencies);
    }


    protected function resolveParams(array $params):array
    {
        return array_map(fn(ReflectionParameter $params)=>$this->getParams($params),$params);
    }

    public function implements(object $reflection,string|array $interfaces):bool
    {
        return ($reflection->implementsInterface($interfaces)) ? true : false;
    }

    protected function getParams(object $params)
    {
        $type = $params->getType();
          if(is_null($type) || $type->isBuiltin())
        {
            if($params->isDefaultValueAvailable())
            {
                return $params->getDefaultValue();
            }
            // throw error

        }
        
        return $this->container->make($params->getType());
    }
}