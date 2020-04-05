<?php
declare(strict_types=1);

/**
 * Here are the variables you can use to extend the application:
 * @var Mamazu\DocumentationParser\FileList $fileList
 * @var Mamazu\DocumentationParser\Application $application
 */

use Mamazu\DocumentationParser\Validator\Php\PhpStanValidator;

$compositeValidator = $application->getValidator()['php'];

$compositeValidator->addValidator(new PhpStanValidator());
