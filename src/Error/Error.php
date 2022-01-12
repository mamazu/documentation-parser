<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Error;

use JsonSerializable;
use Mamazu\DocumentationParser\Parser\Block;

class Error implements JsonSerializable
{
	private string $fileName;

	private int $lineNumber;

	private string $message;

	private ?string $type;

	public function __construct(string $fileName, int $lineNumber, string $message, ?string $type = null)
	{
		$this->fileName = $fileName;
		$this->lineNumber = $lineNumber;
		$this->message = $message;
		$this->type = $type;
	}

	public function getMessage(): string
	{
		if ($this->type === null) {
			return $this->message;
		}
		return "[{$this->type}] {$this->message}";
	}

	public function getFileName(): string
	{
		return $this->fileName;
	}

	public function getLineNumber(): int
	{
		return $this->lineNumber;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * @return array<string, string|int|null>
	 */
	public function jsonSerialize(): array
	{
		return [
			'fileName' => $this->getFileName(),
			'lineNumber' => $this->getLineNumber(),
			'type' => $this->getType(),
			'message' => str_replace('"', '\'', $this->message),
		];
	}

	public static function errorFromBlock(Block $block, int $offset, string $message): self
	{
		return new self($block->getFileName(), $block->getRelativeLineNumber() + $offset, $message, $block->getType());
	}
}
