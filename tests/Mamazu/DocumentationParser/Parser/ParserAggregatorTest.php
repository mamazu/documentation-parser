<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Parser;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Parser\ParserAggregator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ParserAggregatorTest extends TestCase
{
	private ParserAggregator $parserAggregator;

	protected function setUp(): void
	{
		$this->parserAggregator = new ParserAggregator([]);
	}

	public function testAParser(): void
	{
		$this->assertInstanceOf(ParserInterface::class, $this->parserAggregator);
	}

	public function testParsesEverything(): void
	{
		$this->assertTrue($this->parserAggregator->canParse('somefile'));
	}

	public function testCanAddAParser(): void
	{
		/** @var ParserInterface|MockObject $parser */
		$parser = $this->createMock(ParserInterface::class);

		$this->parserAggregator->addParser('php', $parser);

		$this->assertTrue($this->parserAggregator->canParse('test.php'));
	}

	public function testDeligatesTheParsing(): void
	{
		/** @var ParserInterface|MockObject $parser1 */
		$parser1 = $this->createMock(ParserInterface::class);
		$parser1
			->expects($this->once())
			->method('canParse')
			->with('hello.md')
			->willReturn(true)
		;
		$parser1
			->expects($this->once())
			->method('parse')
			->with('hello.md')
			->willReturn([new Block('hello.md', '', 0, 'php')])
		;

		/** @var ParserInterface|MockObject $parser2 */
		$parser2 = $this->createMock(ParserInterface::class);
		$parser2->method('canParse')->with('hello.md')->willReturn(false);
		$parser2
			->expects($this->never())
			->method('parse')
		;

		$this->parserAggregator = new ParserAggregator([
			'md' => $parser1,
			'rst' => $parser2,
		]);

		$this->assertCount(1, $this->parserAggregator->parse('hello.md'));
	}

	public function testReturnsAnEmptyArrayIfNoParserIsDefined(): void
	{
		$this->assertSame([], $this->parserAggregator->parse('hello.md'));
	}
}
