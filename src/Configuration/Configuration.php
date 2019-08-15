<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Configuration;

use Mamazu\DocumentationParser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class Configuration
{
    /** @var array<string> */
    private $paths;

    /** @var array<string, string> */
    private $validators;

    /** @var array<string, string> */
    private $parsers;

    public function __construct(array $paths, array $handler, array $parser)
    {
        $this->paths      = $paths;
        $this->validators = $handler;
        $this->parsers    = $parser;

        $this->instanciateObjects();
    }

    public static function fromFile(string $fileName): self
    {
        $content = \Safe\file_get_contents($fileName);
        $json = \Safe\json_decode($content, true);

        return new self($json['paths'], $json['validators'], $json['parser']);
    }

    private function instanciateObjects()
    {
        foreach ($this->parsers as $type => &$parser) {
            $this->addParser($type, $parser);
        }
        unset($parser);

        foreach ($this->validators as $type => &$validator) {
            $this->addValidator($type, new $validator());
        }
        unset($validator);

    }

    public function addParser(string $type, ParserInterface $parser)
    {
        $this->parsers[$type] = $parser;
    }

    public function getParsers(): array
    {
        return $this->parsers;
    }

    public function addValidator(string $type, ValidatorInterface $validator) {
        $this->validators[$type] = $validator;
    }

    public function getValidator(string $type): ValidatorInterface
    {
        return new $this->validators[$type];
    }

    /**
     * @return array<string>
     */
    public function getFiles(): array
    {
        return array_map(
            static function (string $fileName): string {
                return __DIR__ . '/../../bin/' . $fileName;
            },
            $this->paths
        );
    }
}
