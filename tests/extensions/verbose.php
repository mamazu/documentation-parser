<?php

declare(strict_types=1);
use Mamazu\DocumentationParser\Application;
use Mamazu\DocumentationParser\FileList;
use Mamazu\DocumentationParser\Output\FormatterInterface;
use Mamazu\DocumentationParser\Output\VerboseFormatter;

/**
 * Here are the variables you can use to extend the application:
 * @var FileList $fileList
 * @var Application $application
 * @var FormatterInterface $formatter
 */

$formatter = new VerboseFormatter();
