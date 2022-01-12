<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class ValidatorAggregator implements ValidatorInterface
{
	/**
	 * @var array<ValidatorInterface>
	 */
	private array $validators = [];

	/**
	 * @param array<ValidatorInterface> $parsers
	 */
	public function __construct(array $parsers = [])
	{
		foreach ($parsers as $parserName => $parser) {
			$this->addValidator($parserName, $parser);
		}
	}

	public function addValidator(string $validatorName, ValidatorInterface $validator): void
	{
		$this->validators[$validatorName] = $validator;
	}

	public function validate(Block $block): array
	{
		$type = $block->getType();
		if (! array_key_exists($type, $this->validators)) {
			return [];
		}
		/** @var ValidatorInterface $validator */
		$validator = $this->validators[$type];

		return $validator->validate($block);
	}
}
