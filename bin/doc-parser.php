#!/usr/bin/php
<?php

// Example: php doc-parser.php <files to parse>
if ($argc < 2) {
    echo 'Example usage: php doc-parser.php <files to parse>';
    die(1);
}

include __DIR__.'/../vendor/autoload.php';

use Mamazu\DocumentationParser\Application;
use Mamazu\DocumentationParser\Output\Formatter;
use Mamazu\DocumentationParser\Parser\MarkdownParser;
use Mamazu\DocumentationParser\SystemAbstraction\CommandLineRunner;
use Mamazu\DocumentationParser\Validator\CompositeValidator;
use Mamazu\DocumentationParser\Validator\Php\PhpClassExistsValidator;
use Mamazu\DocumentationParser\Validator\Php\PHPValidator;
use Mamazu\DocumentationParser\Validator\XMLValidValidator;
use PhpParser\ParserFactory;

$arguments = $argv;
array_shift($arguments);
try {
    $application = new Application(
        [
            new MarkdownParser(),
        ],
        [
            'php' =>
                new CompositeValidator(
                    [
                        new PHPValidator(new CommandLineRunner()),
                        new PHPClassExistsValidator(
                            (new ParserFactory)->create(ParserFactory::PREFER_PHP7),
                            'class_exists'
                        ),
                    ]
                ),
            'xml' => new XMLValidValidator(),
        ]
    );
    $output = $application->parse($arguments);
//    echo (new Formatter())->format($output);
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage());
    die($throwable->getCode());
}
