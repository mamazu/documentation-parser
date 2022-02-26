<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Error;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
	private Error $error;

	protected function setUp(): void
	{
		$this->error = new Error('some_filename.php', 10, 'Error occurred');
	}

	public function testHasAMessage(): void
	{
		$this->assertSame('Error occurred', $this->error->getMessage());
	}

	public function testHasATypedMessage(): void
	{
		$this->error = new Error('file.php', 10, 'Error', 'php');
		$this->assertSame('[php] Error', $this->error->getMessage());
	}

	public function testHasALineNumber(): void
	{
		$this->assertSame(10, $this->error->getLineNumber());
	}

	public function testHasAFileName(): void
	{
		$this->assertSame('some_filename.php', $this->error->getFileName());
	}

	public function testCanBeSerialized(): void
	{
		$this->assertSame([
			'fileName' => 'some_filename.php',
			'lineNumber' => 10,
			'type' => null,
			'message' => 'Error occurred',
		], $this->error->jsonSerialize());
	}

	public function testCanBeCreatedFromBlock(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$block->method('getFileName')->willReturn('abc.de');
		$block->method('getRelativeLineNumber')->willReturn(10);
		$block->method('getType')->willReturn('php');

		$this->error = Error::errorFromBlock($block, 10, 'Some message');

		$this->assertSame('abc.de', $this->error->getFileName());
		$this->assertSame('[php] Some message', $this->error->getMessage());
		$this->assertSame(20, $this->error->getLineNumber());
	}
}
