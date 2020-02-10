<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Error\Error;
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
     * @return array<Error>
     */
    public function parse(FileList $fileList): array
    {
        $validationErrors = [];
        foreach ($fileList->getAllValidFiles() as $fileName) {
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
