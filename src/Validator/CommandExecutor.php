<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;

final class CommandExecutor implements ValidatorInterface
{
    /** @var string */
    private $command;

    /** @var string */
    private $formatString;

    public function __construct(string $command, string $formatString)
    {
        $this->command = $command;
        $this->formatString = $formatString;
    }

    private function prepareCommand(string $fileName): string {
        return str_replace('$f$', $fileName, $this->command);
    }

    public function validate(Block $block): array
    {
        file_put_contents('/tmp/code', $block->getContent());

        $output = [];
        exec($this->prepareCommand('/tmp/code'), $output);

        /** @var Error[] */
        $errors = [];
        foreach ($output as $outputLine) {
            $match = [];
            if (preg_match($this->formatString, $outputLine, $match) !== 0) {
                $error = $this->parseMatch($match);
                if ($error !== null) {
                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    private function parseMatch(array $match): ?Error
    {
        return null;
    }
}
