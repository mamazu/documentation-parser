<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Configuration;

use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class Configuration
{
    /** @var array<string> */
    private $paths;

    /** @var array<string, string> */
    private $handler;

    /** @var array<string, string> */
    private $parsers;

    public function __construct(array $paths, array $handler, array $parser)
    {
        $this->paths = $paths;
        $this->handler = $handler;
        $this->parsers = $parser;

        $this->instanicateParsers();
    }

    public static function fromFile(string $fileName): self
    {
        $content = \Safe\file_get_contents($fileName);
        $json = \Safe\json_decode($content, true);

        return new self($json['paths'], $json['handler'], $json['parser']);
    }

    private function instanicateParsers(): void
    {
        foreach ($this->parsers as $type => &$parser) {
            $parser = new $parser();
        }
    }

    public function getParsers(): array
    {
        return $this->parsers;
    }

    public function getHandler(string $type): ValidatorInterface
    {
        return new $this->handler[$type]();
    }

    /**
     * @return array<string>
     */
    public function getFiles(): array
    {
        return array_map(
            function (string $fileName): string {
                return __DIR__ . '/../../bin/' . $fileName;
            },
            $this->paths
        );
    }
}
