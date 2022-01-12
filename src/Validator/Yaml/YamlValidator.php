<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator\Yaml;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

final class YamlValidator implements ValidatorInterface
{
    /** @var Parser */
    private Parser $parser;

    public function __construct(?Parser $parser = null)
    {
        $this->parser = $parser ?? new Parser();
    }

    /** @inheritDoc */
    public function validate(Block $block): array
    {
        try {
            $this->parser->parse($block->getContent());
        } catch (ParseException $exception) {
            // If the entire document is invalid $exception->getParsedLine() will be -1 so we just set it to 0
            $line = max(0, $exception->getParsedLine());
            return [Error::errorFromBlock($block, $line, $exception->getMessage())];
        }

        return [];
    }
}
