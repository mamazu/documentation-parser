<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

class Error {
    public function __construct(string $fileName, int $lineNumber, string $message) {
        $this->fileName = $fileName;
        $this->lineNumber = $lineNumber;
        $this->message =$message;
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
}