<?php

// Example: php doc-parser.php <config-path>
if ($argc !== 2) {
    echo "Example usage: php doc-parser.php <config-path>";
    die(1);
}

include __DIR__.'/../vendor/autoload.php';

use Mamazu\DocumentationParser\Configuration\Configuration;
use Mamazu\DocumentationParser\Application;
use Mamazu\DocumentationParser\Output\Formatter;

$configuration = Configuration::fromFile($argv[1]);
$application = new Application($configuration);
echo (new Formatter())->format($application->parse());