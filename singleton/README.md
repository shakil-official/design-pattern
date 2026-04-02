# Singleton Pattern: Comprehensive QA Guide

This document covers everything you need to know about the Singleton pattern, from basic concepts to expert-level architecture constraints.

---

## 🟢 Basic Level

**What is the Singleton design pattern?**
It is a creational design pattern that ensures a class has only one single instance and provides a global point of access to that instance.

**Why is Singleton considered a creational design pattern?**
Because it explicitly deals with the mechanism of object creation, modifying the standard instantiation process to restrict it to a single object.

**What problem does Singleton solve?**
It solves the problem of restricting object creation, ensuring that shared resources (like database connections, file handlers, or configuration states) do not conflict, duplicate, or waste memory.

**How do you implement a Singleton in PHP?**
1. Private/Protected constructor.
2. Private/Protected `__clone()` method.
3. Public `__wakeup()` that throws an Exception.
4. Static `$instance` property to hold the object.
5. Static `getInstance()` method to access the object.

**Why is the constructor private in a Singleton?**
To prevent other classes from randomly creating new instances using the `new` operator.

**Why do we use a static instance variable?**
Static variables belong to the class level, not the object scope. They persist throughout the application lifecycle, allowing us to cache the single object reference.

**Why is the getInstance() method static?**
Because we need to call it without first having an object instance (`Singleton::getInstance()`). 

**Can a Singleton class be instantiated using `new`?**
No, from the outside, calling `new` throws a fatal error because the constructor is private/protected. However, from *inside* the class (e.g., inside `getInstance()`), `new self()` or `new static()` works fine.

**What happens if the constructor is public in Singleton?**
Anyone can use `new ClassName()` to create secondary instances, completely defeating the purpose of the pattern.

**What is lazy initialization in Singleton?**
It means the instance is only created the *first time* `getInstance()` is called, rather than automatically when the PHP script starts. This saves memory if the singleton is never used.

**What is eager initialization in Singleton?**
Creating the instance automatically upon application boot regardless of whether it's used. (Not fully natively supported or common in PHP, unlike Java).

**What is the difference between Singleton and static class?**
A static class is just a collection of static methods/properties. A Singleton is an actual *object*, meaning it can implement interfaces, extend other classes, and be passed around as a typed parameter.

**When should you use Singleton?**
For managing central, shared resources (Database connections, central Loggers, Configuration Registries).

**When should you avoid Singleton?**
When you need to maintain different states, when writing highly testable code, or when you are just trying to create global variables lazily.

**Is Singleton global state? Why?**
Yes. Since anyone can access the exact same instance from anywhere, any modification to its properties changes the state for the entire application.

---

## 🟡 Intermediate Level

**Why should `__clone()` be private in Singleton?**
To prevent another developer from making a copy of your singleton object by using `$copy = clone Singleton::getInstance()`.

**Why should `__wakeup()` be restricted in Singleton?**
Serialization allows objects to be converted to strings and back to objects. Calling `$unserialized = unserialize($serializedSingleton)` creates a *new* instance in memory. Throwing an exception in `__wakeup()` blocks this.

**Can Singleton be broken? How?**
Yes. Through PHP's `ReflectionClass` (which can make the constructor public temporarily) or if `__clone` / `__wakeup` are poorly implemented.

**How does serialization break Singleton?**
`unserialize()` entirely bypasses the `__construct()` method and assigns a fresh space in memory, resulting in two instances natively running side-by-side.

**How do you prevent Singleton from being cloned/unserialized?**
Implement `private function __clone() {}` and `public function __wakeup() { throw new \Exception(); }`.

**Can Singleton be inherited?**
Yes, but you must use **Late Static Binding** (`new static()` instead of `new self()`, and track instances in a static array keyed by `get_called_class()`) so that parent and child classes get their own separate singleton instances.

**What is Multiton pattern?**
A variation that manages a dictionary (key-value) of singletons. Instead of one instance globally, you have one instance *per key* (e.g., `$db1 = Db::getInstance('mysql'); $db2 = Db::getInstance('sqlite');`).

**Difference between Singleton and Multiton?**
Singleton = Exactly 1 instance. Multiton = 1 instance per specific key/identifier.

**Is Singleton thread-safe?**
In standard, single-threaded PHP (PHP-FPM/CLI), thread safety isn't an issue. However, in extensions like pthreads or Swoole, basic singletons are **not** thread-safe.

**How do you make Singleton thread-safe?**
By using Mutex locks ensuring only one thread can execute the `new static()` instruction at a time.

**What is double-checked locking in Singleton?**
An optimization where you check if the instance is null, then acquire a lock, then check if it's null *again*, before finally creating the object. (Useful for multithreading).

**What is the disadvantage of Singleton?**
They introduce hidden dependencies, make unit testing incredibly difficult, and tightly couple application logic to specific class structures.

**How does Singleton affect unit testing?**
Because singletons persist memory across function calls, tests are no longer isolated. Data mutated in "Test A" might bleed into "Test B", causing random failures.

**Why Singleton is considered anti-pattern sometimes?**
Because it violates Dependency Inversion and promotes global state. Modern architecture prefers Dependency Injection (DI).

**Can Singleton hold state? / What happens if it stores mutable state?**
Yes, but mutating state in a singleton means that changes happen globally. Unintended side-effects easily occur if Component A changes the singleton state expected by Component B.

**Can you reset Singleton instance?**
Not normally. You would need to add a specialized `public static function reset() { self::$instance = null; }` just for testing purposes.

**Can dependency injection replace Singleton?**
Absolutely. You assign the class a standard public constructor, and instruct your DI Container (like Laravel's Service Container) to only ever inject the exact same shared object instance.

---

## 🔵 Advanced Level

**How does Singleton behave in multi-process PHP environments?**
In standard PHP-FPM, memory is entirely isolated between requests. Request A and Request B each get their own separate singleton instance. They cannot share data this way. 

**Singleton vs Dependency Injection Container — which is better?**
DI Containers are massively preferred. They maintain the benefits of shared memory per request while allowing easy testing, decoupling, and mocking.

**How do you implement Singleton with late static binding?**
Replace `self::$instance = new self()` with `static::$instance[get_called_class()] = new static()`.

**What is per-request Singleton in PHP?**
This is the default! The PHP execution cycle destroys all objects (including singletons) the moment the HTTP request finishes sending its response.

**Can you implement Singleton using traits?**
Yes. A `SingletonTrait` containing the `$instance`, constructor, and `getInstance()` is a popular way to reduce boilerplate code across multiple singleton classes.

**How to implement Singleton with constructor arguments?**
Extremely risky. If `getInstance($arg)` is used, it only respects the argument the *first* time it's called. Instead, singletons should be parameterless or configured separately `Config::getInstance()->set($args)`.

**Can you implement Singleton using enum (PHP 8.1+)?**
Yes natively! Enums are guaranteed to be singletons by the engine. `enum DB { case INSTANCE; }` is a true, unbreakable singleton in PHP.

**How do you test Singleton classes?**
Usually by using PHP's `ReflectionProperty` to forcefully set the private `self::$instance` property to `null` during `setUp()` and `tearDown()` of your test classes.

**How does Singleton impact SOLID principles?**
- **Violates SRP:** Manages business logic AND its own structural lifecycle.
- **Violates DIP:** Consumers rely on a concrete class (`Config::getInstance()`) instead of abstract interfaces.

**How to mock Singleton in unit tests?**
Almost impossible natively unless you forcefully inject a mock using Reflection or use a tool specifically designed to intercept static method calls (like Mockery's `alias:` or `overload:`). 

**Singleton vs Service Locator?**
A Service Locator is a registry that *provides* dependencies (often singletons). A Singleton is the actual object managing itself.

**Singleton in Laravel — is it recommended?**
Creating *native* singletons with `getInstance()` is highly discouraged in Laravel. You should use `app()->singleton(MyClass::class, ...)` which containerizes the class as a shared service instead.

**What is the lifecycle of Singleton in PHP-FPM?**
Request Starts → Boot → First `getInstance()` object occupies RAM → Work finishes → Request Dies → RAM completely flushed.

**Singleton memory implications in long-running workers (Swoole/RoadRunner)?**
Because workers don't die after requests, singletons stay in RAM forever! If you append arrays/logs inside the singleton, the server will quickly run out of memory (Memory Leak).

**Singleton vs global variables?**
Singletons are object-oriented and tightly encapsulate their data with getters/setters. Global variables are procedural and vastly more dangerous.

**Can Singleton be immutable?**
Yes, by defining all properties as `readonly` (PHP 8.1+) and not defining any setter methods.

**What is Monostate pattern vs Singleton?**
Monostate allows developers to call `new Monostate()` as many times as they want, but internally, all data properties are static. Different objects; exact same shared memory under the hood.

---

## 🔴 Tricky / Expert Questions

**Can you break Singleton using Reflection?**
Yes. 
```php
$reflection = new ReflectionClass(Singleton::class);
$constructor = $reflection->getConstructor();
$constructor->setAccessible(true);
$newBypassInstance = $reflection->newInstanceWithoutConstructor(); 
```

**How to prevent reflection from breaking Singleton?**
Inside the private `__construct()`, add:
```php
if (self::$instance !== null) throw new \Exception("Instance already exists!");
```

**Can tracking be serialized intentionally?**
Yes, if you remove your override to `__wakeup()`, or if you implement the `Serializable` interface. But you would be breaking the pattern.

**Can Singleton create hidden global state bugs?**
Yes, if Request Component A sets an auth token in the singleton, and Request Component B accidentally utilizes that auth token thinking it was blank.

**What is registry pattern vs Singleton?**
A registry is an object that stores global instances of *other* objects. Essentially, the registry itself is often a singleton, tracking non-singleton objects.

**Should database connection be Singleton always?**
Absolutely not. Modern apps scale using Read Replicas (one connection for reading, one for writing) and Connection Pools. Singletons limit you to exactly one connection to exactly one DB.

**Singleton in async PHP (Swoole/RoadRunner) issues?**
The biggest issue is **State Bleed**. If User A logs in, and the `AuthSingleton` stores their ID... User B making an entirely separate web request an hour later might be served User A's data because the PHP process never rebooted. For async PHP, Singletons must strictly be entirely *stateless*.

**How to reset Singleton during testing natively?**
The cleanest testable way is adding an application environment check:
```php
public static function _testingReset() {
    if (env('APP_ENV') === 'testing') {
        self::$instance = null;
    }
}
```

**Singleton vs shared service in DI container?**
Shared services in DI are strictly superior. You write a standard PHP class (public constructor, no weird static properties) and ask the Container controller to only ever create one of them. This allows complete testability, 100% loose coupling, and no global state bleed.

**When replacing Singleton improves architecture?**
Any time you move away from strict local Singletons to Dependency Formatted Service Containers, you instantly improve testability, adhere to SOLID principles, and support microservice scaling safely.
