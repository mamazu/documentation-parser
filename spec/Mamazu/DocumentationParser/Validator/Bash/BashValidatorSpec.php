<?php

namespace spec\Mamazu\DocumentationParser\Validator\Bash;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpSpec\ObjectBehavior;

class BashValidatorSpec extends ObjectBehavior
{
	public function let(): void
	{
		$this->beConstructedWith('bash');
	}

	public function it_is_a_validator(): void
	{
		$this->shouldBeAnInstanceOf(ValidatorInterface::class);
	}

	public function it_validates_a_block_with_no_errors(Block $block): void
	{
		$block->getContent()->shouldBeCalled()->willReturn('echo "Hello World"');

		$this->validate($block)->shouldReturn([]);
	}

	public function it_validates_a_block_with_errors(Block $block): void
	{
		$block->getContent()->shouldBeCalled()->willReturn('echo "Hello World');
		$block->getFileName()->shouldBeCalled()->willReturn('test.sh');
		$block->getRelativeLineNumber()->shouldBeCalled()->willReturn(10);
		$block->getType()->shouldBeCalled()->willReturn('bash');

		$this->validate($block)->shouldBeLike([
			new Error('test.sh', 11, 'unexpected EOF while looking for matching `"\'', 'bash'),
			new Error('test.sh', 12, 'syntax error: unexpected end of file', 'bash'),
		]);
	}
}
