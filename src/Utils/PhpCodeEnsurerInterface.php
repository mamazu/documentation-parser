<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Utils;


interface PhpCodeEnsurerInterface
{
    public function putPhpCodeToFile(string $sourceCode, string $fileName): void;

    public function getPHPCode(string $sourceCode): string;
}