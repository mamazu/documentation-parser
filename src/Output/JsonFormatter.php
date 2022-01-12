<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Output;

use Webmozart\Assert\Assert;

final class JsonFormatter implements FormatterInterface
{
	public function format(array $output): string
	{
		if (count($output) === 0) {
			return '';
		}

		$json = \json_encode($output, JSON_PRETTY_PRINT);
		Assert::string($json);

		return $json;
	}
}
