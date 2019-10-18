<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use function array_merge;

final class CompositeValidator implements ValidatorInterface
{
    /** @var array<ValidatorInterface> */
    private $validators;

    public function __construct(array $validators) {
        $this->validators = $validators;
    }

     /** {@inheritDoc} */
    public function validate(Block $block): array
    {
        $error = [];
        foreach($this->validators as $validator) {
            $error = array_merge($error, $validator->validate($block));
        }

        return $error;
    }
}
