# traindown-php

Traindown is a [Markdown](https://daringfireball.net/projects/markdown/) inspired language that helps athletes easily document their training.

See [the website](https://traindown.com/) for more information and how to use it from a user's perspective.

**Version**: this library currently aims to support v1.2.1 of the [specification.](https://github.com/traindown/spec)

## Work In Progress

Please understand that this library is still a work in progress and right now only implements the spec and some basic functions to access the resulting data.
There aren't even tests for it yet.

There are most certainly still bugs to be worked out.

Future releases will contain more functionality to access the resulting data as well as transform it into other formats, such as JSON.

## Installation

```
composer install traindown/traindown-php
```

Right now, the only requirements are PHP > 8.0 and the Mbstring extension. **This is not set in stone.**

## Use

Basic usage is fairly straight forward:

```php
$file = '
@ 2021-01-01

* This is an example note!

# example metadata key: value

Bench Press:
  500
';

$parser = new Parser();
$document = $parser->parse($file);

foreach($document->getSessions() as $session) {
    // do something... 
}

```

You can run `$parser->parse($file)` on multiple files without instantiating a new `Parser`.
The internal state will reset on each call.

### Errors

In the basic setup, unknown and unexpected tokens are silently ignored and the parser will continue with the rest of the document. These bad tokens are accessible on the `Parser` object itself:

```php
$parser->hasBadTokens(); // bool
$parser->getBadTokens(); // array of the bad tokens
```

For example, when parsing the string `bw+25a`, two tokens are generated: a `T_LOAD` with a value of 'bw+25' and a `T_UNKNOWN` with a value of 'a'.
This follows the spec for bodyweight loads, but does differ in behavior from the JS implementation which would accept the 'a' as part of the bodyweight value.

If you would rather an exception be thrown, pass `true` when instantiating the `Parser` and any bad token will immediately throw a `BadTokenException`.

```php
$file = '
# Bad metadata
';

$parser = new Parser(true);

$document = $parser->parse($file); // Throws BadTokenException
```

The `BadTokenException` holds the token in question.
If the token is `TokenType::T_UNKNOWN`, this it is an 'unknown' token, unrecognizable to the lexer.
If the token is any other type, it is an 'unexpected' token, appearing in a place where it should not.

### The Traindown Objects

This library parses the traindown string into a series of traindown objects from the `Traindown\Traindown` namespace.

Think of it like a tree where the `Document` is the root.
This contains `Sessions` which contains `Movements` which contains `Performances`.
All of those have metadata in the form of both `Notes` (single string comments) as well as `Data` (key-value pairs, referred to as "metadata" in the spec) and their respective getters and setters through the `HasMetadata` trait.

#### Document

The `Document` has these methods:

```php
$document->getSessions(); // array of Session

$document->addSession(Session $session); // to add a single Session
```

#### Session

The `Session` has these methods:
```php
$session->getDate(); // DateTime
$session->hasDefaultDate(); // bool, true if date failed to parse
$session->getMovements(); // array of Movement

$session->setDatetime(Token $token); // Takes a Token of TokenType::T_DATETIME
$session->addMovement(Movement $movement); // to add a single Movement
```

The value of the T_DATETIME passed in (and thus what your users can enter in their documents) is simply parsed by `new DateTime($value)` and is therefore very forgiving. For a refresher on what formats are accepted, see [the manual.](https://www.php.net/manual/en/datetime.formats.php)
If a value cannot be properly rendered, then a datetime of `now` is set and the `defaultDate` property is set to true.

#### Movement

The `Movement` has these methods:
```php
$movement->getName(); // string
$movement->isSuperset(); // bool
$movement->getSequence(); // int
$movement->getPerformances(); // array of Performance

$movement->addPerformance(Performance $performance); // to add a single Performance
```

The constructor for a `Movement` takes in a `TokenType::T_MOVEMENT` and a sequence number to determine its order in the document.

#### Performance

The `Performance` has these methods:

```php
$performance->getLoad(); // ?float, default is null because it must be set
$performance->getReps(); // float, defaults to 1 if not set
$performance->getSets(); // float, defaults to 1 if not set
$performance->getFails(); // float, defaults to 0 if not set
$performance->getRaw(string $prop); // ?float, returns the property without default value, good for checking if a prop was manually set or not.

$performance->isValid(); // bool, returns true if load has been set

$performance->setLoad(Token $token); // Takes a token of TokenType::T_LOAD
$performance->setReps(Token $token); // Takes a token of TokenType::T_REPS
$performance->setSets(Token $token); // Takes a token of TokenType::T_SETS
$performance->setFails(Token $token); // Takes a token of TokenType::T_FAILS
```

### Data

The `Data` (referred to as metadata in the spec) has these methods:

```php
$data->getKey(); // string
$data->getValue(); // string
$data->getDataPair(); // [$key => $value]
```

The constructor for a `Data` takes in a token of `TokenType::T_METADATA`.

#### Note

The `Note` has these methods:

```php
$note->getValue(); // string
```

The constructor for a `Note` takes in a token of `TokenType::T_NOTE`.