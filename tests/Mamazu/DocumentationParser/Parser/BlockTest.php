<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Parser;

use Mamazu\DocumentationParser\Parser\Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
	private Block $block;

	protected function setUp(): void
	{
		$this->block = new Block('some_file.php', 'Content', 10, 'php');
	}

	public function testHasAFileName(): void
	{
		$this->assertSame('some_file.php', $this->block->getFileName());
	}

	public function testHasAContent(): void
	{
		$this->assertSame('Content', $this->block->getContent());
	}

	public function testHasARelativeLineNumber(): void
	{
		$this->assertSame(10, $this->block->getRelativeLineNumber());
	}

	public function testHasAType(): void
	{
		$this->assertSame('php', $this->block->getType());
	}
}
