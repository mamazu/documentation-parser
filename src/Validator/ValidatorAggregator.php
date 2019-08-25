<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\Validator\ValidatorInterface;

class ValidatorAggregator implements ValidatorInterface
{
    /** @var array<ValidatorInterface> */
    private $validators;

    public function __construct(array $parsers = []) {
        foreach($parsers as $parserName => $parser) {
            $this->addValidator($parserName, $parser);
        }
    }

    public function addValidator(string $validatorName, ValidatorInterface $validator): void
    {
        $this->validators[$validatorName] = $validator;
    }

    /** {@inheritDoc} */
    public function validate(Block $block): array
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->validators[$block->getType()];

        return $validator->validate($block);
    }
}
