<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\FormatException;

class EnvValidator implements ValidatorInterface
{
	private Dotenv $envParser;

	public function __construct()
	{
		$this->envParser = new Dotenv();
	}

	public function validate(Block $block): array
	{
		try {
			$this->envParser->parse($block->getContent(), $block->getFileName());
		} catch (FormatException $e) {
			$messageLines = explode("\n", $e->getMessage());
			return [
				Error::errorFromBlock($block, $e->getContext()->getLineno(), $messageLines[0]),
			];
		}
		return [];
	}
}
