<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Parser;

class MarkdownParser implements ParserInterface
{
    public function canParse(string $fileName): bool
    {
        return strtolower(substr($fileName, -2)) === 'md';
    }

    public function parse(string $fileName): array
    {
        $lines = \Safe\file($fileName, FILE_IGNORE_NEW_LINES);
        assert(is_array($lines));
        $blocks = [];

        /** @var null|int $beginLine */
        $beginLine = null;
        $type = '';
        $content = '';
        foreach ($lines as $lineNumber => $lineContent) {
            if (strpos($lineContent, '```') === 0) {
                if ($beginLine === null) {
                    $content = '';
                    $type = substr($lineContent, 3);
                    $beginLine = $lineNumber;
                } else {
                    $blocks[] = new Block($fileName, trim($content), $beginLine + 1, $type);
                    $beginLine = null;
                }
            } else {
            $content .= $lineContent . "\n";
            }
        }

        return $blocks;
    }
}
