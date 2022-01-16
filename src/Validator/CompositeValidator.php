<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use function array_merge;
use Mamazu\DocumentationParser\Parser\Block;

final class CompositeValidator implements ValidatorInterface
{
	/**
	 * @var array<ValidatorInterface>
	 */
	private array $validators;

	private bool $continueValidationOnFailure;

	/**
	 * @param array<ValidatorInterface> $validators
	 */
	public function __construct(array $validators, bool $continueValidationOnFailure = false)
	{
		$this->validators = $validators;
		$this->continueValidationOnFailure = $continueValidationOnFailure;
	}

	public function addValidator(ValidatorInterface $validator): void
	{
		$this->validators[] = $validator;
	}

	public function validate(Block $block): array
	{
		$error = [];
		foreach ($this->validators as $validator) {
			$newErrors = $validator->validate($block);
			if ($this->continueValidationOnFailure || count($newErrors) === 0) {
				$error = array_merge($error, $newErrors);
			} else {
				return $newErrors;
			}
		}

		return $error;
	}
}
