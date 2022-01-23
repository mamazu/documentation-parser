<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileList
{
	/**
	 * @var array<string>
	 */
	private array $files = [];

	public function addFile(string $filePath): void
	{
		if (is_dir($filePath)) {
			$this->addDirectory($filePath);

			return;
		}
		$this->files[] = $filePath;
	}

	public function removeFile(string $filePath): void
	{
		$this->files = array_values(array_filter(
			$this->files,
			static function (string $f) use ($filePath): bool {
				return $f !== $filePath;
			}
		));
	}

	/**
	 * @return iterable<string>
	 */
	public function getAllFiles(): iterable
	{
		yield from $this->files;
	}

	/**
	 * @return array<string>
	 */
	public function getAllValidFiles(): array
	{
		$validFiles = [];
		foreach ($this->files as $file) {
			if (! file_exists($file)) {
				trigger_error('Could not find file: ' . $file, E_USER_WARNING);
				continue;
			}
			$validFiles[] = $file;
		}

		return $validFiles;
	}

	private function addDirectory(string $directory): void
	{
		$directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
		foreach ($directoryIterator as $file) {
			/** @var \SplFileInfo $file */
			if ($file->isDir()) {
				continue;
			}

			$this->files[] = $file->getPathname();
		}
	}
}
