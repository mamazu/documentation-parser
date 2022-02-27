<?php

declare(strict_types=1);

/**
 * Here are the variables you can use to extend the application:
 * @var Mamazu\DocumentationParser\FileList $fileList
 * @var Mamazu\DocumentationParser\Application $application
 * @var Mamazu\DocumentationParser\Output\FormatterInterface $formatter
 */

$validators = &$application->getValidator();

unset($validators['bash']);
unset($validators['sh']);
