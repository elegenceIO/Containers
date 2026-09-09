<?php
namespace ElegenceIO\Containers;

class Registration
{
    public function __construct(private Container $container, private string $abstract)
    {
    }
    
    public function  alias(string $alias) : self {
        
        $this->container->alias($alias,$this->abstract);
        return $this;
    }

    public function allowOverride(string $abstract):self
    {
        $this->container->allowOverride($abstract);
        return $this;
    }
}