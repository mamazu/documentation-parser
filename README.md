# Doc Parser (WIP)
[![Build Status](https://travis-ci.org/mamazu/documentation-parser.svg?branch=master)](https://travis-ci.org/mamazu/documentation-parser)

Every project needs documentation which is usually accompanied by code snippets that show how a component is integrated. The problem is how does the project ensure that this code runs and that the code is up to date? Then this is your tool.

## Installation
For a php application:
1. `composer require --dev mamazu/documentation-validator`
2. `php vendor/bin/doc-parser.php <arguments>`

For any other application:
Download the most recent release and run `php doc-parser.php <arguments>`

## Extending it
This plugin will have a Symfony integration.

### Configuration
The configuration contains three keys: `validators`, `parsers` and `paths`. 

* In the **paths** key, you specify the paths that should be checked.
* In the **parser** key, you define a list of parsers. The key of the parser is currently unused.
* In the **validator** key you can define the validators that should be used for a given type. The key corresponds to language that is being checked.

With parser and validators you need to define how to instantiate it with an array. In the array the first item is the class name of the class you want to instanciate. After that follows the list of arguments. If the class has a dependency on another class then the argument is an array as well. For example:
```json
{
    "validators": {
        "php": [
            "Mamazu\\DocumentationParser\\Validator\\PHPValidator",
            ["Mamazu\\DocumentationParser\\SystemAbstraction\\CommandLineRunner"]
        ]
    }
}
```

### Adding parsers and validator
Adding parsers to it: Create a class that implements the `ParserInterface` and add it to the application

Adding validators to it: Create a class that implements the `ValidatorInterface` and add it to the application