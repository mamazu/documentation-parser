<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Parser;

class Block
{
    /** @var string */
    private $fileName;

    /** @var string */
    private $content;

    /** @var int */
    private $relativeLineNumber;

    /** @var string */
    private $type;

    public function __construct(string $fileName, string $content, int $relativeLineNumber, string $type)
    {
        $this->fileName = $fileName;
        $this->content = $content;
        $this->relativeLineNumber = $relativeLineNumber;
        $this->type = $type;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRelativeLineNumber(): int
    {
        return $this->relativeLineNumber;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
