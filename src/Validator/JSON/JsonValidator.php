<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator\JSON;

use JsonException;
use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class JsonValidator implements ValidatorInterface
{
	public function validate(Block $block): array
	{
		try {
			$content = json_decode($block->getContent(), true, 1024, JSON_THROW_ON_ERROR);
		} catch (JsonException $e) {
			return [
				Error::errorFromBlock($block, 1, $e->getMessage()),
			];
		}

		return [];
	}
}
