<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Parser\Parser;

class IgnoredFileParser implements ParserInterface
{
	/**
	 * @var array<string>
	 */
	private array $ignoredExtensions;

	/**
	 * @param array<string> $ignoredExtensions
	 */
	public function __construct(array $ignoredExtensions = ['png', 'jpg', 'jpeg'])
	{
		$this->ignoredExtensions = $ignoredExtensions;
	}

	public function canParse(string $fileName): bool
	{
		$extension = pathinfo($fileName, PATHINFO_EXTENSION);

		return in_array(strtolower($extension), $this->ignoredExtensions, true);
	}

	public function parse(string $fileName): array
	{
		return [];
	}
}
