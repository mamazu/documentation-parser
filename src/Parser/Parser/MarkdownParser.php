<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Block;

class MarkdownParser implements ParserInterface
{
    public function canParse(string $fileName): bool
    {
        return strtolower(substr($fileName, -2)) === 'md';
    }

    /** {@inheritDoc} */
    public function parse(string $fileName): array
    {
        $lines = \Safe\file($fileName, FILE_IGNORE_NEW_LINES);
        return array_merge(
            $this->parseCodeBlocks($fileName, $lines),
            $this->parseBlockTags($fileName, $lines)
        );
    }

    /**
     * Parses the blocks from markdown like these:
     *
     * ```bash
     * echo Hello World
     * ```
     *
     * @param string $fileName
     * @param string[]  $lines
     *
     * @return Block[]
     */
    public function parseCodeBlocks(string $fileName, array $lines): array
    {
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
                } else if (is_int($beginLine)) {
                    $blocks[] = new Block($fileName, trim($content), $beginLine + 1, $type);
                    $beginLine = null;
                } else {
                    throw new \InvalidArgumentException('The line numbers have to be an int or null');
                }
            } else {
                $content .= $lineContent."\n";
            }
        }

        return $blocks;
    }

    /**
     * Parses the code tags from markdown with the following syntax:
     *
     * <code lang="bash">echo Hello World</code>
     *
     * @param string $fileName
     * @param string[]  $lines
     *
     * @return Block[]
     */
    public function parseBlockTags(string $fileName, array $lines): array
    {
        return [];
    }
}
