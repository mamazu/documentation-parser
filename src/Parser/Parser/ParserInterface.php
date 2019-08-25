<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Block;

interface ParserInterface
{
    public function canParse(string $fileName): bool;

    /** @return array<Block> */
    public function parse(string $fileName): array;
}
