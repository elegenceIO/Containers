<?php
namespace ElegenceIO\Containers;

use Exception;

class ContainerRegistry
{
    private static ?Container $instance = null;

    public static function set(Container $instance):void
    {
        static::$instance = $instance;
    }

    public static function has():bool
    {
        return (self::$instance !== null) ? true : false;
    }
    
    public static function clear():void
    {
        static::$instance = null;
    }

    public static function get():Container
    {
        if(!static::$instance)
        {
            throw new Exception("Container has not been booted up at the moment");
        }

        return static::$instance;
    }

}