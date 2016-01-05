![DC\JSON - Typed JSON](logo.png)

Allow serializing directly to classes using only phpDoc and type hints.

## Installation

```
$ composer install dc/json
```

Or add it to `composer.json`:

```json
"require": {
	"dc/json": "0.*"
}
```

```
$ composer install
```

**This package suggests `dc/ioc` and `dc/cache`, but it really is a very strong recommendation. It will be painful or slow to use without it.**

[![Build status](http://teamcity.digitalcreations.no/app/rest/builds/buildType:(id:DcJson_Build)/statusIcon)](http://teamcity.digitalcreations.no/viewType.html?buildTypeId=DcJson_Build&guest=1 "Build status")

# Getting started

Get hold of a new `\DC\JSON\Serializer` and start serializing:

```php
class Cat {
    /** @var string */
    public $name;
}
$json = '[{ "name": "Sniffles" }, { "name": "Snuggles" }]'; 
$serializer = new \DC\JSON\Serializer();
$cats = $serializer->deserialize($catsJson, '\Cat[]');
```

# How classes are constructed

Classes are constructed in this manner:

1. Look for the property names in the constructor. Use the constructor to fill in those values.
2. Look for public setters of the form `setX` for the remaining values (this is controlled by convention, which you can 
   override). Use them.
3. Look for public properties for the remaining values.

So, given this class:

```php
class Cat {
    private $name;
    private $age;
    public $paws = 4;

    function __construct($name) {
        $this->name = $name;
    }
    
    function setAge($age) {
        $this->age = $age;
    }
}
```

...and this JSON:

```json
{
    "name": "Snuggles",
    "age": 6,
    "paws": 4
}
```

...These two are equivalent:

```php
$cat = $serializer->deserialize($json, '\Cat');
// or
$cat = new \Cat("Snuggles");
$cat->setAge(6);
$cat->paws = 4;
```

# Performance

It is no secret that `json_decode` and `json_encode` is a lot faster than this package. In fact, we use those internally
for the actual serialization. The magic of this package gets applied before serialization and after deserialization.

Using a combination of type hints and documentation, we are smart about constructing your objects the way they were
intended to be constructed. But, parsing your documentation is resource intensive, therefore it is good practice to
heed a few rules:

1. Try to keep the serializer instance around for multiple serializations. All of the reflection happens only the first 
   time a class is encountered.
2. Install `dc/cache` and `dc/cache-memcache`, and provide it to the serializer. If you do, all the information obtained
   from the reflection is cached for a while.
   
When using a primed cache, deserializing is about 4-5 times as slow a `json_decode`.  