<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser;

class FileList
{
    /** @var array<string> */
    private $files = [];

    public function addFile(string $filePath): void
    {
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
