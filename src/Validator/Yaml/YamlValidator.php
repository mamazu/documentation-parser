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
		$cleanedContent = $this->normalizeIndentation($block);
		try {
			$this->parser->parse($cleanedContent);
		} catch (ParseException $exception) {
			// If the entire document is invalid $exception->getParsedLine() will be -1 so we just set it to 0
			$line = max(0, $exception->getParsedLine());
			return [Error::errorFromBlock($block, $line, $exception->getMessage())];
		}

		return [];
	}

	private function normalizeIndentation(Block $block): string
	{
		$lineContent = explode("\n", $block->getContent());
		$indendation = '';
		foreach ($lineContent as $line) {
			if ($line === '') {
				continue;
			}

			$offset = 0;
			while ($offset < strlen($line)) {
				$char = $line[$offset];
				if (! ctype_space($char)) {
					break;
				}
				$offset++;
			}
			$indendation = substr($line, 0, $offset);

			break;
		}

		// Speed up if there is no indentation.
		if ($indendation === '') {
			return $block->getContent();
		}

		$result = '';
		foreach ($lineContent as $lineIndex => $line) {
			$count = 1;
			if (strpos($line, $indendation) !== 0) {
				$trueLineNumber = $block->getRelativeLineNumber() + $lineIndex;
				throw new \InvalidArgumentException(
					sprintf(
						'Expected indentation "%s" in file %s on line %d',
						$this->printIndentation($indendation),
						$block->getFileName(),
						$trueLineNumber
					)
				);
			}
			$result = str_replace($indendation, '', $line, $count) . PHP_EOL;
		}

		return $result;
	}

	private function printIndentation(string $indentation): string
	{
		return str_replace(["\t", ' '], ['\\t', '…'], $indentation);
	}
}
