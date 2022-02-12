<?php

declare(strict_types=1);
namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;

class NoOpValidator implements ValidatorInterface
{
	public function validate(Block $block): array
	{
		return [];
	}
}
