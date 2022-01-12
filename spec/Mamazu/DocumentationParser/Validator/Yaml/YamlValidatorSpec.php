<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator\Yaml;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlValidatorSpec extends ObjectBehavior
{
	public function let(Parser $parser): void
	{
		$this->beConstructedWith($parser);
	}

	public function it_is_a_validator(): void
	{
		$this->shouldImplement(ValidatorInterface::class);
	}

	public function it_validates_a_block(Parser $parser, Block $block): void
	{
		$block->getContent()->willReturn('test: true');

		$parser->parse('test: true')->shouldBeCalled();

		$this->validate($block)->shouldReturn([]);
	}

	public function it_validates_a_block_and_adds_errors_if_parsing_fails(
		Parser $parser,
		Block $block
	): void {
		$block->getContent()->willReturn('test: true');
		$block->getRelativeLineNumber()->willReturn(10);
		$block->getType()->willReturn('yaml');
		$block->getFileName()->willReturn('test.md');

		$parseException = new ParseException('Invalid YAML', 10);

		$parser->parse('test: true')->shouldBeCalled()->willThrow($parseException);

		$result = $this->validate($block);
		$result->shouldHaveCount(1);
		$result[0]->shouldBeLike(new Error('test.md', 20, 'Invalid YAML at line 10', 'yaml'));
	}

	public function it_adds_an_error_if_the_whole_document_is_invalid(
		Parser $parser,
		Block $block
	): void {
		$block->getContent()->willReturn('test: true');
		$block->getRelativeLineNumber()->willReturn(10);
		$block->getType()->willReturn('yaml');
		$block->getFileName()->willReturn('test.md');

		$parseException = new ParseException('Invalid YAML', -1);

		$parser->parse('test: true')->shouldBeCalled()->willThrow($parseException);

		$result = $this->validate($block);
		$result->shouldHaveCount(1);
		$result[0]->shouldBeLike(new Error('test.md', 10, 'Invalid YAML', 'yaml'));
	}
}
