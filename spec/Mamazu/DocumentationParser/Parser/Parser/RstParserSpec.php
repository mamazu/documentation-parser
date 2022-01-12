<?php

namespace spec\Mamazu\DocumentationParser\Parser\Parser;

use Gregwar\RST\Document;
use Gregwar\RST\Environment;
use Gregwar\RST\ErrorManager;
use Gregwar\RST\HTML\Nodes\CodeNode;
use Gregwar\RST\Parser;
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RstParserSpec extends ObjectBehavior
{
	public function let(Parser $parser): void
	{
		$this->beConstructedWith($parser);
	}

	public function it_is_a_parser(): void
	{
		$this->shouldImplement(ParserInterface::class);
	}

	public function it_only_allows_rst_files(): void
	{
		$this->canParse('docs.rst')->shouldReturn(true);
		$this->canParse('hello.py')->shouldReturn(false);
		$this->canParse('hello.RsT')->shouldReturn(true);
	}

	public function it_parses_code_blocks(
		Parser $parser,
		Document $document,
		Environment $environment,
		ErrorManager $errorManager,
		CodeNode $codeNode
	): void {
		$parser->getEnvironment()->willReturn($environment);
		$environment->getErrorManager()->willReturn($errorManager);

		$errorManager->abortOnError(false)->shouldBeCalled();

		$parser->parseFile('documentation.rst')->shouldBeCalled()->willReturn($document);
		$document->getNodes(Argument::type(\Closure::class))->willReturn([$codeNode]);
		$codeNode->getValue()->willReturn('echo "PHP";');
		$codeNode->getStartingLineNumber()->willReturn(12);
		$codeNode->getLanguage()->willReturn('php');

		$result = $this->parse('documentation.rst');
		$result->shouldHaveCount(1);
		$result[0]->getFileName()->shouldBe('documentation.rst');
		$result[0]->getContent()->shouldBe('echo "PHP";');
		$result[0]->getRelativeLineNumber()->shouldBe(12);
		$result[0]->getType()->shouldBe('php');
	}
}
