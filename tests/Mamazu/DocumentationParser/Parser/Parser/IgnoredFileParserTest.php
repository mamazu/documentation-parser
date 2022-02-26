<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Parser\IgnoredFileParser;
use PHPUnit\Framework\TestCase;

class IgnoredFileParserTest extends TestCase
{
	private IgnoredFileParser $ignoredFileParser;

	protected function setUp(): void
	{
		$this->ignoredFileParser = new IgnoredFileParser();
	}

	public function testParsesTheFilesWithExtensions(): void
	{
		$parser = $this->ignoredFileParser = new IgnoredFileParser(['png']);
		$this->assertTrue($this->ignoredFileParser->canParse('hello.png'));
		$this->assertFalse($this->ignoredFileParser->canParse('hello.jpg'));
	}

	public function testDoesNotReturnAnyBlocks(): void
	{
		$this->assertSame([], $this->ignoredFileParser->parse('hello.png'));
	}
}
