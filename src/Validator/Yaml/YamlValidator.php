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
	private Parser $parser;

	public function __construct(?Parser $parser = null)
	{
		$this->parser = $parser ?? new Parser();
	}

	public function validate(Block $block): array
	{
		$cleanedContent = $this->normalizeIndentation($block->getContent());
		try {
			$this->parser->parse($cleanedContent);
		} catch (ParseException $exception) {
			// If the entire document is invalid $exception->getParsedLine() will be -1 so we just set it to 0
			$line = max(0, $exception->getParsedLine());
			return [Error::errorFromBlock($block, $line, $exception->getMessage())];
		}

		return [];
	}

	private function normalizeIndentation(string $content): string
	{
		$lineContent = explode("\n", $content);
		$offset = 0;
		foreach ($lineContent as $line) {
			if ($line === '') {
				continue;
			}

			while ($offset < strlen($line)) {
				$char = $line[$offset];
				if (! ctype_space($char)) {
					break;
				}
				$offset++;
			}
			break;
		}

		return implode(
			"\n",
			array_map(
				static fn (string $line): string => substr($line, $offset),
				$lineContent
			)
		);
	}
}
