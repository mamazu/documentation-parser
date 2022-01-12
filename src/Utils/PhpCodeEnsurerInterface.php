<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Utils;


interface PhpCodeEnsurerInterface
{
    /**
     * Put the source code generated `getPHPCode` in the file specified
     *
     * @param string $sourceCode
     * @param string $fileName
     *
     * @return void
     */
    public function putPhpCodeToFile(string $sourceCode, string $fileName): void;

    /**
     * Makes the php executable, eg. adding a &lt;?php
     *
     * @param string $sourceCode
     *
     * @return string
     */
    public function getPHPCode(string $sourceCode): string;
}
