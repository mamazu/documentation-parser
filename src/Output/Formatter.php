<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Validator\Error;

class Formatter
{
    public function format(array $output): string
    {

        return implode(
            "\n",
            array_map(
                function(Error $error) {
                    return $error->getFileName().':'.$error->getLineNumber().' ---- '.$error->getMessage();
                },
                $output
            )
        );
    }
}
