# Doc Parser (WIP)
Every project needs documentation which is usually accompanied by code snippets that show how a component is integrated. The problem is how does the project ensure that this code runs and that the code is up to date? Then this is your tool.

## Installation
For a php application:
1. `composer require --dev mamazu/documentation-validator`
2. `php vendor/bin/doc-parser.php <arguments>`

For any other application:
Download the most recent release and run `php doc-parser.php <arguments>`

## Extending it
This plugin will have a Symfony integration.

Adding parsers to it: Create a class that implements the `ParserInterface` and add it to the application
Adding validators to it: Create a class that implements the `ValidatorInterface` and add it to the application