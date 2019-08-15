<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Validator\Error;

class Formatter
{
    public function format(array $output): string
    {
        $string = '';
        foreach ($output as $error) {
            /** @var Error $error */
            $string .= $error->getFileName() . ':' . $error->getLineNumber() . ' ---- ' . $error->getMessage() . "\n";
        }

        return $string;
    }
}
