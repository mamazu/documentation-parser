#!/usr/bin/php
<?php

// Example: php doc-parser.php <files to parse>
if ($argc !== 2) {
    echo 'Example usage: php doc-parser.php <config-path>';
    die(1);
}

include __DIR__.'/../vendor/autoload.php';

use Mamazu\DocumentationParser\Application;
use Mamazu\DocumentationParser\Output\Formatter;
use Mamazu\DocumentationParser\Parser\MarkdownParser;
use Mamazu\DocumentationParser\SystemAbstraction\CommandLineRunner;
use Mamazu\DocumentationParser\Validator\CompositeValidator;
use Mamazu\DocumentationParser\Validator\PHPValidator;
use Mamazu\DocumentationParser\Validator\XMLValidValidator;

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
                    ]
                ),
            'xml' => new XMLValidValidator(),
        ]
    );
    echo (new Formatter())->format($application->parse($arguments));
} catch (Throwable $throwable) {
    die($throwable->getCode());
}
