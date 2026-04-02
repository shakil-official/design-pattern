# Singleton Pattern

## What is it?
The Singleton pattern is a creational design pattern that guarantees a class has **only one instance** during the execution of a program, and provides a global point of access to it.

## Why is it used?
1. **Reduce Memory Footprint**: No matter how many times you try to instantiate or call the class, only a single object exists in memory.
2. **Shared State**: All components in your application share the same object, meaning any changes to its state are instantly visible across the entire application.
3. **Controlled Access**: It prevents other code from overwriting the single instance.

## When to use it? (Use Cases)
- **Database Connections**: You usually only need one active connection to a given database. Creating multiple connections per request creates overhead and eats up connection limits.
- **Logging Services**: A logger often writes to a single file. Using a single instance ensures that writes don't conflict and are handled centrally.
- **Configuration/Settings Manager**: An object that loads config variables from a file/database once, and makes them available globally without needing to re-read from disk.
- **Caching**: Holding frequently accessed data in memory during the app lifecycle.

## How to use it? (Implementation Details)

To properly implement a Singleton in PHP (as seen in the `SingletonBase` class in this project), several constraints must be placed on the class:

1. **Protected/Private Constructor (`__construct`)**: Prevents creating new instances globally using the `new` keyword.
2. **Protected/Private Clone (`__clone`)**: Prevents making copies of the object using the `clone` keyword.
3. **Public Wakeup throwing Exception (`__wakeup`)**: Prevents creating instances via `unserialize()`. Since PHP 8, `__wakeup` should be public, but it should manually throw an `Exception` to block unserialization.
4. **Static Array/Variable (`$instance`)**: Holds the instance(s) of the class in memory.
5. **Static Access Method (`getInstance()`)**: The global entry point. It creates the instance if it doesn't exist, and returns the cached instance.

### Late Static Binding in `SingletonBase`

In this project, `SingletonBase` provides a reusable singleton base class utilizing **Late Static Binding (`new static()`)**:

```php
public static function getInstance()
{
    $class = get_called_class(); // Gets the name of the child class (e.g., Db or Logger)

    if (!isset(static::$instance[$class])) {
        static::$instance[$class] = new static();
    }

    return static::$instance[$class];
}
```

By making `$instance` an array keyed by the class name (`$class`), multiple different classes (like `Logger`, `Db`) can extend `SingletonBase`. They will all independently maintain their own separate singletons instead of overwriting each other.
