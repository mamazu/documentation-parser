<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Validator\Error;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class Application
{
    /** @var array<ParserInterface> */
    private $parser;

    /** @var array<ValidatorInterface> */
    private $validator;

    public function __construct(array $parser, array $validator)
    {
        $this->parser = $parser;
        $this->validator = $validator;
    }

    /**
     * @param array<string> $files
     * @return array<Error>
     */
    public function parse(array $files): array
    {
        $validationErrors = [];
        foreach ($files as $fileName) {
            if (!\file_exists($fileName)) {
                echo 'Could not find file: '.$fileName.PHP_EOL;
                continue;
            }

            $blocks = $this->getDocumentationBlocks($fileName);
            foreach ($blocks as $block) {
                $errors = $this->validateBlock($block);
                $validationErrors = array_merge($validationErrors, $errors);
            }
        }

        return $validationErrors;
    }

    private function getDocumentationBlocks(string $fileName): array
    {
        foreach ($this->parser as $parser) {
            if ($parser->canParse($fileName)) {
                return $parser->parse($fileName);
            }
        }

        return [];
    }

    /** @return array<Error> */
    private function validateBlock(Block $block): array
    {
        $type = $block->getType();
        $handler = $this->validator[$type];

        return $handler->validate($block);
    }
}
