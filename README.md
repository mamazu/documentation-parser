# Documentation validator
[![Build Status](https://travis-ci.org/mamazu/documentation-parser.svg?branch=master)](https://travis-ci.org/mamazu/documentation-parser)

Every project needs documentation which is usually accompanied by code snippets that show how a component is integrated. The problem is how does the project ensure that this code runs and that the code is up to date? Then this is your tool.

## Installation
`composer require --dev mamazu/documentation-validator`

`php vendor/bin/doc-parser.php <files to check>`

## How to configure it
Configuration of the software is done in code. There is an extension point in the binary which can be used to inject any piece of code. You can run the application with the `-c` option and provide a file name and this file will be run before the validation is executed.

### Parsers
* Markdown parser: Parses markdown and **only** extracts the block comments like this:
> \```php
>
>echo "ABC";
>
>\```

### Validators
* CompositeValidator: Validates all of it's children passed into the constructor
* PHP:
    * PhpValidator: Validates if a piece of php code contains valid php (only syntax checking with `php -l`)
    * ClassExistenceValidator: Validates if the classes referenced in the use statement exist
* XMLValidator: Checks if the document contains valid XML

### Adding parsers and validator
Adding parsers to it: Create a class that implements the `ParserInterface` and add it to the application e.g.
```php
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;

class Parser implements ParserInterface {
}
```

Adding validators to it: Create a class that implements the `ValidatorInterface` and add it to the application
```php
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class Validator implements ValidatorInterface {
}
```
