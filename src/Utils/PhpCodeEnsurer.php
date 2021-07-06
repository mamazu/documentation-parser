<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Utils;

use Symfony\Component\Filesystem\Filesystem;

final class PhpCodeEnsurer implements PhpCodeEnsurerInterface
{
    /** @var Filesystem */
    private $fileSystem;

    public function __construct(?Filesystem $fileSystem = null)
    {
        $this->fileSystem = $fileSystem ?? new Filesystem();
    }

    public function putPhpCodeToFile(string $sourceCode, string $fileName): void
    {
        $this->fileSystem->dumpFile($fileName, $this->getPHPCode($sourceCode));
    }

    public function getPHPCode(string $sourceCode): string
    {
        $sourceCode = trim($sourceCode);
        if (strpos($sourceCode, '<?php') === false) {
            return '<?php '.$sourceCode;
        }

        return $sourceCode;
    }
}
