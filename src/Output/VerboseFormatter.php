<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Error\Error;
use Webmozart\Assert\Assert;

class VerboseFormatter implements FormatterInterface
{
	public function format(array $output): string
	{
		return implode(
			"\n",
			array_map(fn (Error $error): string => $this->formatError($error), $output)
		);
	}

	private function formatError(Error $error): string
	{
		$fileContents = file_get_contents($error->getFileName());
		Assert::string($fileContents, 'Could not read file contents of ' . $fileContents);

		$lines = explode("\n", $fileContents);

		$seperator = '========== [' . $error->getType() . '@' . $error->getFileName() . ':' . $error->getLineNumber() . '] ==========';

		$message = "\n${seperator}\n";
		$message .= $lines[$error->getLineNumber() - 2] . "\n";
		$message .= $lines[$error->getLineNumber() - 1] . "\n";
		$message .= "^^^ \e[31m" . $error->getRawMessage() . "\e[0m ^^^\n";
		$message .= ($lines[$error->getLineNumber()] ?? '$EOF$') . "\n";
		$message .= str_repeat('=', strlen($seperator));

		return $message;
	}
}
