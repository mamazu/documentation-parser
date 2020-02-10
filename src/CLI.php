<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser;

class CLI
{
    /** @var string|null */
    private $includeFile;

    /** @var FileList */
    private $filesToParse;

    public function __construct(FileList $fileList, array $parameter)
    {
        $this->filesToParse = $fileList;
        $hasInclude = false;
        foreach ($parameter as $p) {
            if ($p === '-i') {
                $hasInclude = true;
            } elseif ($hasInclude) {
                $this->includeFile = $p;
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

    public function getIncludeFile(): ?string
    {
        return $this->includeFile;
    }
}
