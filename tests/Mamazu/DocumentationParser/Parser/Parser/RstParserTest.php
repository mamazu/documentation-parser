<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Parser\Parser;

use Closure;
use Gregwar\RST\Document;
use Gregwar\RST\Environment;
use Gregwar\RST\ErrorManager;
use Gregwar\RST\HTML\Nodes\CodeNode;
use Gregwar\RST\Parser;
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Parser\Parser\RstParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RstParserTest extends TestCase
{
	private RstParser $rstParser;

	/**
	 * @var Parser|MockObject
	 */
	private $parser;

	protected function setUp(): void
	{
		$this->parser = $this->createMock(Parser::class);
		$this->rstParser = new RstParser($this->parser);
	}

	public function testAParser(): void
	{
		$this->assertInstanceOf(ParserInterface::class, $this->rstParser);
	}

	public function testOnlyAllowsRstFiles(): void
	{
		$this->assertTrue($this->rstParser->canParse('docs.rst'));
		$this->assertFalse($this->rstParser->canParse('hello.py'));
		$this->assertTrue($this->rstParser->canParse('hello.RsT'));
	}

	public function testParsesCodeBlocks(): void
	{
		/** @var Document|MockObject $document */
		$document = $this->createMock(Document::class);
		/** @var Environment|MockObject $environment */
		$environment = $this->createMock(Environment::class);
		/** @var ErrorManager|MockObject $errorManager */
		$errorManager = $this->createMock(ErrorManager::class);

		/** @var CodeNode|MockObject $codeNode */
		$codeNode = $this->createConfiguredMock(CodeNode::class, [
			'getValue' => 'echo "PHP";',
			'getStartingLineNumber' => 12,
			'getLanguage' => 'php',
		]);

		$this->parser->method('getEnvironment')->willReturn($environment);
		$environment->expects($this->atLeastOnce())->method('setErrorManager');
		$this->parser->expects($this->atLeastOnce())->method('parseFile')->with($this->equalTo('documentation.rst'))->willReturn($document);

		$document->method('getNodes')
			->with($this->isInstanceOf(Closure::class))
			->willReturn([$codeNode])
	   ;

		$result = $this->rstParser->parse('documentation.rst');
		$this->assertCount(1, $result);
		$this->assertSame('documentation.rst', $result[0]->getFileName());
		$this->assertSame('echo "PHP";', $result[0]->getContent());
		$this->assertSame(12, $result[0]->getRelativeLineNumber());
		$this->assertSame('php', $result[0]->getType());
	}
}
