<?php
namespace ElegenceIO\Containers;

class Registration
{
    public function __construct(private Container $container, private string $abstract)
    {
    }

    public function locked(string $interface) : self {
        
        $this->container->lockTo($this->abstract,$interface);
        return $this;
    }

    public function  alias(string $alias) : self {
        $this->container->alias($alias,$this->abstract);
        return $this;
    }
}