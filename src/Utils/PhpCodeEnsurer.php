<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Utils;

use Symfony\Component\Filesystem\Filesystem;

final class PhpCodeEnsurer implements PhpCodeEnsurerInterface
{
	private Filesystem $fileSystem;

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

		// Finding partial classes: meaning the documentation only contains class bodies
		// in this case we just define a class around it for it to be valid php
		$matches = [];
		if (
			preg_match('/(class .*\s*{)?.*(public|private|protected) function .*/u', $sourceCode, $matches) > 0 &&
			$matches[1] === ''
		) {
			$sourceCode = <<<PHP
namespace Mamazu\DocumentationParser;
class AnonymousClassThatWeNeedForItToBeValidPhp { ${sourceCode} }
PHP;
		}

		// Adding the php stag in front
		if (strpos($sourceCode, '<?php') !== 0) {
			return '<?php ' . $sourceCode;
		}

		return $sourceCode;
	}
}
