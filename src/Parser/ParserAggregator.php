<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Parser;

use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;

class ParserAggregator implements ParserInterface
{
    /** @var array<ParserInterface> */
    private $parsers = [];

    public function __construct(array $parsers = []) {
        foreach($parsers as $parserName => $parser) {
            $this->addParser($parserName, $parser);
        }
    }

    public function addParser(string $parserName, ParserInterface $parser): void
    {
        $this->parsers[$parserName] = $parser;
    }

    public function canParse(string $fileName): bool
    {
        // todo: maybe add logic to check if the parser list matches the filename
        return true;
    }

    /** {@inheritDoc} */
    public function parse(string $fileName): array
    {
        foreach ($this->parsers as $parser) {
            /** @var ParserInterface $parser */
            if ($parser->canParse($fileName)) {
                return $parser->parse($fileName);
            }
        }
        return [];
    }
}
