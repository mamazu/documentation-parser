<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\SystemAbstraction;

interface CommandLineRunnerInterface
{
    public function run(string $command): array;
}