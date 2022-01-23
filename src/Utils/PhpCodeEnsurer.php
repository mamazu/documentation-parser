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

		if (
			stripos($sourceCode, 'public') === 0 ||
			stripos($sourceCode, 'private') === 0 ||
			stripos($sourceCode, 'protected') === 0
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
