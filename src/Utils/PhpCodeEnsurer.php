<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Utils;

final class PhpCodeEnsurer implements PhpCodeEnsurerInterface
{
    public function putPhpCodeToFile(string $sourceCode, string $fileName): void
    {
        $dirname = dirname($fileName);
        if (!file_exists($dirname)) {
            $this->createDirectory($dirname);
        }
        file_put_contents($fileName, $this->getPHPCode($sourceCode));
    }

    public function getPHPCode(string $sourceCode): string
    {
        $sourceCode = trim($sourceCode);
        if (strpos($sourceCode, '<?php') === false) {
            return '<?php '.$sourceCode;
        }

        return $sourceCode;
    }

    private function createDirectory(string $dirname): void
    {
        if (!mkdir($dirname, 0777, true) && !is_dir($dirname)) {
            throw new \InvalidArgumentException(sprintf('Directory "%s" was not created', $dirname));
        }
    }
}
