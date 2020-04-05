<?php

use Mamazu\DocumentationParser\Validator\Php\PhpStanValidator;

$compositeValidator = $application->getValidator()['php'];

$compositeValidator->addValidator(new PhpStanValidator());
