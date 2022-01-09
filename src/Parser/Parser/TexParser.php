<?php

namespace Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Block;

class TexParser implements ParserInterface {
    public function canParse(string $fileName): bool
    {
        return strtolower(substr($fileName, -3)) === 'tex';
    }

    public function parse(string $fileName): array
    {
        $fileContent = file($fileName, FILE_IGNORE_NEW_LINES);

        $codeBlockBegin = null;
        $type = '';
        $content = '';
        $blocks = [];

        foreach($fileContent as $lineIndex => $line) {
            $matches = [];

            //trying to match the following pattern: \begin{lstlisting}[language=Python]
            if (preg_match('/\\\\begin{lstlisting}(\\[language=(?<lang>\w+)\\])?\\s?(?<rest>[^\\\\]*)/', $line, $matches)) {
                $codeBlockBegin = $lineIndex;
                $type = strtolower($matches['lang']);
                $content = $matches['rest'];
            }

            if ($codeBlockBegin !== null && strpos($line, '\\end{lstlisting}') !== false) {
                $blocks[] = new Block(
                    $fileName, 
                    rtrim($content),
                    $codeBlockBegin + 1,
                    $type
                );
                $codeBlockBegin = null;
                $type = '';
            }

            if ($codeBlockBegin !== null && $codeBlockBegin !== $lineIndex) {
                $content .= $line . "\n";
            }
        }
        
        if ($codeBlockBegin !== null) {
            throw new \InvalidArgumentException(
                'You have an unopend codeblock in your code starting on line: '. ($codeBlockBegin + 1)
            );
        }
        
        return $blocks;
    }
}