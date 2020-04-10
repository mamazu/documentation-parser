<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Output;

final class JsonFormatter implements FormatterInterface
{
    /** @inheritDoc */
    public function format(array $output): string
    {
        if (count($output) === 0) {
            return '';
        }

        return \Safe\json_encode($output, JSON_PRETTY_PRINT);
    }
}