<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser;

use Exception;
use InvalidArgumentException;

class FileList
{
    /** @var array<string> */
    private $files = [];

    public function addFile(string $filePath): void
    {
        if (is_dir($filePath)) {
            throw new InvalidArgumentException('Directories are not implemented yet. Please specify the files individually.');
        }
        $this->files[] = $filePath;
    }

    public function removeFile(string $filePath): void
    {
        $this->files = array_values(array_filter(
            $this->files,
            static function (string $f) use ($filePath) {
                return $f !== $filePath;
            }
        ));
    }

    public function getAllFiles(): iterable
    {
        yield from $this->files;
    }

    public function getAllValidFiles(): array
    {
        $validFiles = [];
        foreach ($this->files as $file) {
            if (!file_exists($file)) {
                trigger_error('Could not find file: '.$file, E_USER_WARNING);
                continue;
            }
            $validFiles[] = $file;
        }

        return $validFiles;
    }
}
