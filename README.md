# Doc Parser (WIP)
[![Build Status](https://travis-ci.org/mamazu/documentation-parser.svg?branch=master)](https://travis-ci.org/mamazu/documentation-parser)

Every project needs documentation which is usually accompanied by code snippets that show how a component is integrated. The problem is how does the project ensure that this code runs and that the code is up to date? Then this is your tool.

## Installation
`composer require --dev mamazu/documentation-validator`

`php vendor/bin/doc-parser.php <files to check>`

## Extending it
This plugin will have a Symfony integration.

## How to configure it
Configuration of the software is done in code. If you want to add a new parser or validator you need to change the instantiation code of the application in the `bin/doc-parser.php` file.

The first list of objects are the parsers that extract the source code out of the documentation. The second list of objects are the validators where the key of the array is the type of source code they validate.

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
Adding parsers to it: Create a class that implements the `ParserInterface` and add it to the application

Adding validators to it: Create a class that implements the `ValidatorInterface` and add it to the application
