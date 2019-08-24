<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser;

use Mamazu\DocumentationParser\Configuration\Configuration;
use Mamazu\DocumentationParser\Parser\Block;

class Application
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function parse(): array
    {
        $validationErrors = [];
        foreach ($this->configuration->getFiles() as $fileName) {
            if (!\file_exists($fileName)) {
                echo 'Could not find file: ' . $fileName . PHP_EOL;
                continue;
            }

            $blocks = $this->getDocumentationBlocks($fileName);
            foreach ($blocks as $block) {
                $validationErrors = array_merge($validationErrors, $this->validateBlock($block));
            }
        }

        return $validationErrors;
    }

    private function getDocumentationBlocks(string $fileName): array
    {
        foreach ($this->configuration->getParsers() as $parser) {
            if ($parser->canParse($fileName)) {
                return $parser->parse($fileName);
            }
        }
    }

    private function validateBlock(Block $block): array
    {
        $type = $block->getType();
        $handler = $this->configuration->getValidator($type);
        $result = $handler->validate($block);

        return $result;
    }
}
