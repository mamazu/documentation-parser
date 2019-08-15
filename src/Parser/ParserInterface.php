<?php
namespace Mamazu\DocumentationParser\Parser;

interface ParserInterface
{
    public function canParse(string $fileName): bool;

    /** @return array<Block> */
    public function parse(string $fileName): array;
}