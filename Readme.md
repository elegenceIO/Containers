# ElegenceIO Container.

## What it is?
elegence io continer package is a core package designed to register content and load it in a global state.

## How to use

**installation**

```php
composer require elegenceIO/containers

// or

git clone https://github.com/elegenceIO/Container
```

**instantiation**
```php
use ElegenceIO\Containers\Container;
use ElegenceIO\Containers\ContainerRegistry;

function app(){
$container = new Container();
// make static access 
ContainerRegistry::set($container);

// Returns the container
return $container
}
```

**Bindings**

* `map()`
    * used for Mapping a container Referene
    * Supports strings Integers arrays and callables 
    * Ideal for Mapping config files.
* `bind()`
    * used to map a new instance when called.
    * must be callable using function() or fn()
* `simgleton()`
    * used to map a single persistant reference
    * must be callable using function() or fn()

**options**

* `locked()`
    * can be used to map an interface to a class
* `alias()`
    provides the ability to map an alias to a class.

## Example Usage

**Creating and Instance**
```php
app()->map(Database::class,new Database());
```

the following example works for both singleton and bind

```php
app()->bind("db",function()
{
    $db = new Database(//Include config file or array data\/);
    return $db;
})

// Optionally

app()->bind("db",fn()=> new Database("config.php"));

```

Returning a container this will activate the container
s
```php
app()->get("db");
```

**Adding Aliases**

```php
app()->bind(Database::class,function()
{
    $db = new Database(//Include config file or array data\/);
    return $db;
})->alias("database")->alias
    

// Want to alias an alias this can be done like to 


app()->alias("db2","database");
// Both db2 and database will work as alias files 
```

**Locking Interface**
Currently this only supports locking one interface, however this can be accomplished like so.

```php
app()->bind("db",function()
{
    $db = new Database(//Include config file or array data\/);
    return $db;
})->locked(Connection::class);

```

`locked()` and `alias()` method can be usentogether format;