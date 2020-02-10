<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Error\Error;

interface ValidatorInterface
{
    /**
     * Validates a block of code and returns an array of errors
     *
     * @return array<Error>
     */
    public function validate(Block $block): array;
}
