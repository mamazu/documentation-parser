# Documentation validator
[![Build Status](https://travis-ci.org/mamazu/documentation-parser.svg?branch=master)](https://travis-ci.org/mamazu/documentation-parser)

Every project needs documentation which is usually accompanied by code snippets that show how a component is integrated. The problem is how does the project ensure that this code runs and that the code is up to date? Then this is your tool.

## Installation
**This is a dev only package. Installing it without dev dependencies will cause problems.**

`composer require --dev mamazu/documentation-validator`

`vendor/bin/doc-parser <files to check>`

## How to configure it
Configuration of the software is done in code. There is an extension point in the binary which can be used to inject any piece of code. You can run the application with the `-i` option and provide a file name and this file will be run before the validation is executed.
>Example: `bin/doc-parser -i my_extension_script.php docs` (order of arguments does not matter.)

An example for adding more validators is given in the `tests/add_phpstan.php` file which also adds the validation rules of phpstan.

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
    * ClassExistenceValidator: Validates if the classes referenced in the use statement exist
    * PhpStanValidator (optional): Validates the code with phpstan
* XML:
    * XMLValidator: Checks if the document contains valid XML

