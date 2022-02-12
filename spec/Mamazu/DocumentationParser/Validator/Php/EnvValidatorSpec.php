<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use PhpSpec\ObjectBehavior;

class EnvValidatorSpec extends ObjectBehavior
{
	public function it_validates_a_valid_file(Block $block)
	{
		$block->getContent()->shouldBeCalled()->willReturn('APP_ENV=test');
		$block->getFileName()->shouldBeCalled()->willReturn('.env');

		$errors = $this->validate($block);
		$errors->shouldHaveCount(0);
	}

	public function it_validates_an_invalid_block(Block $block)
	{
		$block->getContent()->shouldBeCalled()->willReturn('APP_ENV="${something');
		$block->getFileName()->shouldBeCalled()->willReturn('.env');
		$block->getRelativeLineNumber()->shouldBeCalled()->willReturn(0);
		$block->getType()->shouldBeCalled()->willReturn('env');

		$errors = $this->validate($block);
		$errors->shouldHaveCount(1);
		$errors[0]->shouldBeLike(new Error('.env', 1, 'Missing quote to end the value in ".env" at line 1.', 'env'));
	}
}
