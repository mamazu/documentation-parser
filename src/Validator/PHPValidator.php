<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;

class PHPValidator implements ValidatorInterface
{
    private $tempPath = '/tmp/code.php';

    public function validate(Block $block): array
    {
        file_put_contents($this->tempPath, $this->makePhpCode($block->getContent()));

        $returnValue = 0;
        $output = [];
        exec('php -l '.$this->tempPath. ' 2>&1',  $output, $returnValue);

        $errors = array_filter($output, function (string $message): bool {return strpos($message, 'PHP Parse error') === 0; });

        return array_map(function (string $errorMessage) use ($block): Error {
            return $this->parseErrors($block, $errorMessage);
        }, $errors);
    }

    private function makePhpCode(string $sourceCode): string
    {
        if(strpos($sourceCode, '<?php') === false) {
            return '<?php '. $sourceCode;
        }

        return $sourceCode;
    }

    private function parseErrors(Block $block, string $message): Error
    {
        $tempPath = str_replace('/','\\/', $this->tempPath);

        $matches = [];
        preg_match('/(.*) in '.$tempPath.' on line (\d+)/', $message, $matches);
        $message = $matches[1];
        $lineNumber = (int) $matches[2];

        return new Error($block->getFileName(), $block->getRelativeLineNumber() + $lineNumber + 1, $message);
    }
}