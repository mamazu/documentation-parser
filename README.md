# Documentation validator
[![PHP Composer](https://github.com/mamazu/documentation-parser/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/mamazu/documentation-parser/actions/workflows/php.yml)

Every project needs documentation which is usually accompanied by code snippets that show how a component is integrated. The problem is how does the project ensure that this code runs and that the code is up to date? Then this is your tool.

## Installation
**This is a dev only package. Installing it without dev dependencies will cause problems.**

`composer require --dev mamazu/documentation-validator`

`vendor/bin/doc-parser <files to check>`

## How to use it
```text
bin/phpdoc-parser <path> [-i extensionscript.php]

path			Path containing the documentation
-i script.php	Configuration script of the application

Example:
bin/doc-parser docs					# Validate with default configuration
bin/doc-parser docs -i config.php	# Validate with custom configuration
```

The configuration of the extension is loaded before the application starts. For a template checkout the `tests/extensions/delete.php` file. An example for adding more validators is given in the `tests/extensions/add_phpstan.php` file which also adds the validation rules of [PHPstan](https://github.com/phpstan/phpstan).

## Supported parsing formats
[Here](https://github.com/mamazu/documentation-parser/tree/master/src/Parser/Parser) is the full list of parsers that this library supports:

* IgnoredFileParser (this allows ignoring certain extensions like pdf files)
* Markdown (only supporting block comments for now)
* RstParser
* LatexParser (only supporting the `lstlisting` package)

## Validators
* CompositeValidator: Validates all of its children passed into the constructor
* Bash:
    * BashValidator: Validates bash or sh files with the build in spellchecker
* PHP:
    * ClassExistenceValidator: Validates if the classes referenced in the use statement exist
    * PhpStanValidator (optional): Validates the code with PHPstan
    * EnvValidator: Validate the contents of the `.env` files
* XML:
    * XMLValidator: Checks if the document contains valid XML
* YAML / YML:
    * YamlValidator: Checks if the document contains valid Yaml
* JSON:
    * JsonValidator: Default PHP JSON parsing without line numbers of errors
