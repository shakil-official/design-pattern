# Observer Design Pattern — Simple Explanation (PHP)

When one object changes, multiple objects need to react automatically — this is where the Observer Pattern shines.

## 𝗣𝗿𝗼𝗯𝗹𝗲𝗺
You complete an order and need to:
- Send Email
- Send SMS
- Save Log
- Push Notification

Without Observer, everything is tightly coupled inside one class.
Every new feature = modify existing code

## ✅ 𝗦𝗼𝗹𝘂𝘁𝗶𝗼𝗻
Observer Pattern creates a one-to-many dependency.
Subject notifies observers, observers handle their own logic.
Loose coupling. Easy extension. Clean architecture.

### Example (PHP)
```php
interface Observer {
    public function update($data);
}

class EmailObserver implements Observer {
    public function update($data) {
        echo "Send Email: $data\n";
    }
}

class Order {
    private $observers = [];

    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    public function notify($data) {
        foreach ($this->observers as $observer) {
            $observer->update($data);
        }
    }

    public function complete() {
        $this->notify("Order Completed");
    }
}

$order = new Order();
$order->attach(new EmailObserver());
$order->complete();
```

## 𝗕𝗲𝗻𝗲𝗳𝗶𝘁𝘀
- Loose coupling
- Open/Closed principle
- Easy to extend
- Clean separation of concerns

## 𝗥𝗲𝗮𝗹 𝗨𝘀𝗲 𝗖𝗮𝘀𝗲𝘀
- Event systems
- Notification services
- Webhooks
- Logging

---

# Observer Pattern: Comprehensive QA Guide

This document covers common interview questions regarding the Observer pattern, from basic concepts to advanced architectural constraints.

---

## 🟢 Basic Level

**What is the Observer design pattern?**
It is a behavioral design pattern that defines a one-to-many dependency between objects so that when one object changes state, all its dependents are notified and updated automatically.

**Why is Observer considered a behavioral design pattern?**
Because it deals with the communication and responsibilities between objects, specifically how instances interact and share state changes dynamically at runtime.

**What are the key components of the Observer pattern?**
1. **Subject (Publisher/Observable)**: The object being watched. It manages the list of observers and sends notifications.
2. **Observer (Subscriber)**: The object that wants to be notified when the Subject changes.

**What problem does Observer solve?**
It eliminates tight coupling when an object needs to trigger behaviors in other varied objects. Instead of hardcoding all the resulting actions into one class, the subject simply announces "I changed!" and lets interested observers handle their own logic.

**How do you implement an Observer pattern in PHP?**
- Define an `Observer` interface with an `update()` method.
- Define a `Subject` interface (or class) with `attach()`, `detach()`, and `notify()` methods.
- The concrete Subject holds an array of Observers and loops through them in its `notify` method, calling `update()` on each.

**Can an object be both a Subject and an Observer?**
Yes. An object might listen to changes from one part of the system (Observer) and fire its own events to notify other parts of the system (Subject).

**Why is it called the Publish/Subscribe (Pub/Sub) pattern sometimes?**
While inherently similar, Pub/Sub usually introduces an event channel/broker between the publisher and subscriber (fully decoupling them). Standard Observer requires the Subject and Observer to know about each other via the `attach()` method.

**When should you use the Observer pattern?**
When a change to one object requires changing others, and you don't fully know how many objects need to be changed or what those objects are beforehand. (e.g., when a user registers, you must send an email, create a log, and notify the analytics service — add observers for these instead of writing it all in `UserRegistration::register()`).

---

## 🟡 Intermediate Level

**What is standard vs. push vs. pull model in Observer?**
- **Push Model**: The subject passes all the changed data directly to the observer (e.g., `$observer->update($data)`).
- **Pull Model**: The subject notifies the observer that *something* changed, and passes a reference to itself. The observer then queries the subject to pull only the data it needs (e.g., `$observer->update($this); $data = $subject->getState();`).

**Does PHP have built-in support for the Observer pattern?**
Yes! PHP has the built-in `SplSubject` and `SplObserver` interfaces (along with `SplObjectStorage` for tracking them), though many developers prefer to write their own interfaces to use stronger typing for specific data payloads.

**Why use `SplObjectStorage` instead of a simple array for observers?**
`SplObjectStorage` is a built-in object collection that treats objects as keys. This makes it extremely easy to `detach($observer)` because you don't have to search an array for a matching reference; `SplObjectStorage` handles object identity matching natively.

**What is the Open/Closed Principle (SOLID), and how does Observer follow it?**
You can introduce new subscriber classes without having to change the publisher's code whatsoever. The subject keeps working happily with any new observer that implements the interface.

**Can Observer cause memory leaks? (The Lapsed Listener Problem)**
Yes, it's one of the most common issues! If a Subject holds a hard reference to an Observer in its `$observers` array, the Observer cannot be garbage collected even if the rest of the application is done with it. You must systematically `detach()` observers when they are no longer needed.

**What happens if an Observer throws an Exception during `notify()`?**
By default, the loop in `notify()` will break, and subsequent observers in the array will never receive the notification. You should usually wrap the `update()` call in a `try-catch` block within the `notify()` method, or have observers handle their own exceptions.

**Is the order in which observers are notified guaranteed?**
Usually, it shouldn't matter. The core idea of Observer is loose coupling, meaning observers shouldn't rely on being called in a specific order. If order matters, your architecture might need a Chain of Responsibility or a MiddleWare pattern instead.

---

## 🔵 Advanced Level

**Observer vs. Mediator pattern?**
- **Observer**: Defines a one-to-many relationship where a Subject broadcasts to many independent Observers dynamically.
- **Mediator**: Centralizes complex communications between heavily interacting objects. Components talk to the Mediator, and the Mediator talks back. (Observer distributes communication; Mediator centralizes it).

**Observer vs Chain of Responsibility?**
- **Observer**: *All* registered observers get the notification and process it.
- **Chain of Responsibility**: A request goes down a line of handlers until *one* of them processes it and breaks the chain.

**How is Observer implemented in modern frameworks (like Laravel)?**
Frameworks abstract it into **Event Dispatchers** or **Listeners**. Instead of `Subject->attach(Observer)`, you register a mapping in an EventServiceProvider (`'OrderCompletedEvent' => ['SendEmail', 'CreateLog']`). The framework acts as an Event Broker.

**What are the disadvantages of the Observer pattern?**
- **Debuggability:** It can be very hard to follow the control flow. You trigger `complete()` on an Order, and suddenly 5 different things happen deep in other files.
- **Cascading Updates:** A flaw in the Subject could trigger thousands of cascading updates (or even infinite loops if observers change the Subject's state which triggers another notification).

**How do you prevent infinite loops in Observer?**
If an Observer's action modifies the Subject, causing it to `notify()` again, it loops forever. To prevent this, either:
1. Don't allow observers to mutate the subject.
2. Introduce a flag like `isNotifying` to block nested notifications.
3. Batch updates and only emit one notification at the very end of a transaction.

**Asynchronous Observers (Queues)?**
In large apps, firing 10 observers during a web request takes too long. Often, the "Observer" just pushes a job to a Queue (like Beanstalkd, RabbitMQ, Laravel Queues) so the actual work (sending emails/SMS) happens asynchronously on a worker server.
