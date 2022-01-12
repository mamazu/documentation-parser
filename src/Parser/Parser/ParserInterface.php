<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Block;

interface ParserInterface
{
	/**
	 * The function returns true for files that it potentially can parse based on the file name
	 * Eg. Markdown parser can parse all files with .md as extension
	 */
	public function canParse(string $fileName): bool;

	/**
	 * Returns a list of code blocks that the validator needs to validate.
	 *
	 * @return array<Block>
	 */
	public function parse(string $fileName): array;
}
