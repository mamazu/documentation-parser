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
     * @param string   $fileName
     * @param string[] $lines
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
            $patternPosition = strpos($lineContent, '```');
            if ($patternPosition === 0) {
                if ($beginLine === null) {
                    $content = '';
                    $type = substr($lineContent, 3);
                    $beginLine = $lineNumber;
                } elseif (is_int($beginLine)) {
                    $blocks[] = new Block($fileName, trim($content), $beginLine + 1, $type);
                    $beginLine = null;
                } else {
                    throw new \InvalidArgumentException('The line numbers have to be an int or null');
                }
            } elseif (is_int($patternPosition)) {
                trigger_error(
                    sprintf(
                        'Invalid format (%s:%d). The line needs to start with ``` in order to be a code block. SKIPPING',
                        $fileName,
                        $lineNumber + 1
                    ),
                    E_USER_NOTICE
                );
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
     * @param string   $fileName
     * @param string[] $lines
     *
     * @return Block[]
     */
    public function parseBlockTags(string $fileName, array $lines): array
    {
        $blocks = [];
        foreach ($lines as $lineNumber => $lineContent) {
            $matches = [];
            $matchCount = (int)preg_match_all('/<code lang="([^"]+)">(.*?)<\/code>/i', $lineContent, $matches);

            for ($i = 0; $i < $matchCount; $i++) {
                /** @var string[] $match */
                $match = array_column($matches, $i);
                $blocks[] = new Block($fileName, $match[2], $lineNumber + 1, $match[1]);
            }
        }

        return $blocks;
    }
}
