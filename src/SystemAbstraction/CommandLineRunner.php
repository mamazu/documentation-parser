<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\SystemAbstraction;

class CommandLineRunner implements CommandLineRunnerInterface
{
    /** @var bool */
    private $throwExceptionOnNonZeroErrorCode;

    public function __construct(
        bool $throwExceptionOnNonZeroErrorCode = false
    ) {
        $this->throwExceptionOnNonZeroErrorCode = $throwExceptionOnNonZeroErrorCode;
    }

    public function run(string $command): array
    {
        $returnValue = 0;
        $output = [];
        exec($command, $output, $returnValue);

        if ($returnValue !== 0 && $this->throwExceptionOnNonZeroErrorCode) {
            throw new \Exception('Program has exited with error code: '.$returnValue);
        }

        return $output;
    }
}
