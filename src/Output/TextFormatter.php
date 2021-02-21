<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Error\Error;

class TextFormatter implements FormatterInterface
{
    public function format(array $output): string
    {
        return implode(
            "\n",
            array_map(
                static function(Error $error): string {
                    return $error->getFileName().':'.$error->getLineNumber().' ---- '.$error->getMessage();
                },
                $output
            )
        );
    }
}
