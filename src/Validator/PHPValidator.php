<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\SystemAbstraction\CommandLineRunnerInterface;

class PHPValidator implements ValidatorInterface
{
    /** @var CommandLineRunnerInterface */
    private $commandLineRunner;

    public function __construct(CommandLineRunnerInterface $commandLineRunner)
    {
        $this->commandLineRunner = $commandLineRunner;
    }

    /** @var string */
    private $tempPath = '/tmp/code.php';

    public function validate(Block $block): array
    {
        file_put_contents($this->tempPath, $this->makePhpCode($block->getContent()));

        $output = $this->commandLineRunner->run('php -l ' . $this->tempPath . ' 2>&1');

        return array_map(function (string $errorMessage) use ($block): Error {
            return $this->parseErrors($block, $errorMessage);
        }, array_filter($output, static function (string $message): bool {
            return strpos($message, 'PHP Parse error') === 0;
        }));
    }

    private function makePhpCode(string $sourceCode): string
    {
        if (strpos($sourceCode, '<?php') === false) {
            return '<?php ' . $sourceCode;
        }

        return $sourceCode;
    }

    private function parseErrors(Block $block, string $message): Error
    {
        $tempPath = str_replace('/', '\\/', $this->tempPath);

        $matches = [];
        preg_match('/PHP Parse error: (.*) in ' . $tempPath . ' on line (\d+)/', $message, $matches);
        $message = $matches[1];
        $lineNumber = (int) $matches[2];

        return Error::errorFromBlock($block, $lineNumber + 1, $message);
    }
}
