<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator\XML;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpSpec\ObjectBehavior;

class XMLValidValidatorSpec extends ObjectBehavior
{
	public function it_is_a_validator(): void
	{
		$this->shouldImplement(ValidatorInterface::class);
	}

	public function it_produces_no_errors_if_the_xml_is_valid(Block $block): void
	{
		$block->getContent()->shouldBeCalled()->willReturn('
<Hello>
    This is text
</Hello>
')
		;
		$result = $this->validate($block);
		$result->shouldBeArray();
		$result->shouldHaveCount(0);
	}

	public function it_returns_errors_if_the_xml_is_invalid(Block $block): void
	{
		$block->getContent()->shouldBeCalled()->willReturn('
<Hello =>
    This is text
</Hello>
')
		;
		$block->getType()->shouldBeCalled()->willReturn('xml');
		$block->getFileName()->shouldBeCalled()->willReturn('abc.xml');
		$block->getRelativeLineNumber()->shouldBeCalled()->willReturn(10);

		$result = $this->validate($block);
		$result->shouldBeArray();
		$result->shouldHaveCount(4);
	}

	public function it_validates_consecutive_blocks_correctly(Block $block, Block $block2): void
	{
		$block->getContent()->shouldBeCalled()->willReturn('
<Hello =>
    This is text
</Hello>
')
		;
		$block->getType()->shouldBeCalled()->willReturn('xml');
		$block->getFileName()->shouldBeCalled()->willReturn('abc.xml');
		$block->getRelativeLineNumber()->shouldBeCalled()->willReturn(10);

		$result = $this->validate($block);
		$result->shouldBeArray();
		$result->shouldHaveCount(4);

		$block2->getContent()->shouldBeCalled()->willReturn('
<Hello>
    This is text
</Hello>
');

		$this->validate($block2)->shouldHaveCount(0);
	}
}
