<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;

class Error
{
    /** @var string */
    private $fileName;

    /** @var int */
    private $lineNumber;

    /** @var string */
    private $message;

    public function __construct(string $fileName, int $lineNumber, string $message)
    {
        $this->fileName = $fileName;
        $this->lineNumber = $lineNumber;
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public static function errorFromBlock(Block $block, int $offset, string $message): self
    {
        $blockPrefix = '['.$block->getType().'] ';
        return new self($block->getFileName(), $block->getRelativeLineNumber() + $offset - 1, $blockPrefix.$message);
    }
}
