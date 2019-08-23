<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Configuration;

use Mamazu\DocumentationParser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class Configuration
{
    /** @var array<string> */
    private $paths;

    /** @var array<ValidatorInterface> */
    private $validators = [];

    /** @var array<ParserInterface> */
    private $parsers = [];

    protected function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    public static function fromFile(string $fileName): self
    {
        $content = \Safe\file_get_contents($fileName);
        $json    = \Safe\json_decode($content, true);

        $object = new self($json['paths']);
        foreach ($json['parser'] as $type => &$parser) {
            $object->addParser($type, Instanciator::createFromArray($parser));
        }
        unset($parser);

        foreach ($json['validators'] as $type => &$validator) {
            $object->addValidator($type, Instanciator::createFromArray($validator));
        }
        unset($validator);

        return $object;
    }

    public function addParser(string $type, ParserInterface $parser): void
    {
        $this->parsers[$type] = $parser;
    }

    public function getParsers(): array
    {
        return $this->parsers;
    }

    public function addValidator(string $type, ValidatorInterface $validator): void
    {
        $this->validators[$type] = $validator;
    }

    public function getValidator(string $type): ValidatorInterface
    {
        if(!array_key_exists($type, $this->validators)) {
            throw new \InvalidArgumentException('No validator found for "'.$type.'"');
        }
        return $this->validators[$type];
    }

    /**
     * @return array<string>
     */
    public function getFiles(): array
    {
        return array_map(
            static function (string $fileName): string {
                return __DIR__.'/../../bin/'.$fileName;
            },
            $this->paths
        );
    }
}
