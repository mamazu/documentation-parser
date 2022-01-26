<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Utils;

use function strtok;
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
		if ($this->needsToBeWrappedInClass($sourceCode)) {
			if ($this->hasPhpTag($sourceCode)) {
				$sourceCode = substr($sourceCode, 5);
			}

			$sourceCode = <<<PHP
namespace Mamazu\DocumentationParser;
class AnonymousClassThatWeNeedForItToBeValidPhp {
	${sourceCode}
}
PHP;
		}

		if (! $this->hasPhpTag($sourceCode)) {
			return '<?php ' . $sourceCode;
		}

		return $sourceCode;
	}

	private function hasPhpTag(string $sourceCode): bool
	{
		return strpos($sourceCode, '<?php') === 0;
	}

	private function needsToBeWrappedInClass(string $sourceCode): bool
	{
		$token = strtok($sourceCode, " \n\t");

		$needsClassDefintion = false;
		while ($token !== false) {
			if ($this->in_array_case_insensitive(['class', 'trait', 'interface'], $token)) {
				return false;
			}

			if ($this->in_array_case_insensitive(['public', 'private', 'protected'], $token)) {
				$needsClassDefintion = true;
			}

			$token = strtok(" \n\t");
		}

		return $needsClassDefintion;
	}

	/**
	 * @param array<string> $keywords
	 */
	private function in_array_case_insensitive(array $keywords, string $keywordToFind): bool
	{
		foreach ($keywords as $keyword) {
			if (strcasecmp($keyword, $keywordToFind) === 0) {
				return true;
			}
		}
		return false;
	}
}
