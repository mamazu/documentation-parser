<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class CLI
{
	private ?string $includePath = null;

	private FileList $filesToParse;

	/**
	 * @param array<string> $parameter
	 */
	public function __construct(FileList $fileList, array $parameter)
	{
		$this->filesToParse = $fileList;
		$hasInclude = false;
		foreach ($parameter as $p) {
			if ($p === '-i') {
				$hasInclude = true;
			} elseif ($hasInclude) {
				$this->includePath = $p;
				$hasInclude = false;
			} else {
				$this->filesToParse->addFile($p);
			}
		}
	}

	public function getFilesToParse(): FileList
	{
		return $this->filesToParse;
	}

	/**
	 * @return array<string>
	 */
	public function getIncludePaths(): array
	{
		if ($this->includePath === null) {
			return [];
		}

		if (is_file($this->includePath) && file_exists($this->includePath)) {
			return [$this->includePath];
		}

		if (is_dir($this->includePath)) {
			$fileIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->includePath));
			$regex = new RegexIterator($fileIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

			return array_keys(iterator_to_array($regex));
		}

		return [];
	}
}
