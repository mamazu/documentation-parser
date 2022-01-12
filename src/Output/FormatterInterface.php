<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Error\Error;

interface FormatterInterface
{
	/**
	 * Formats the list of errors
	 *
	 * @param array<Error> $output
	 */
	public function format(array $output): string;
}
