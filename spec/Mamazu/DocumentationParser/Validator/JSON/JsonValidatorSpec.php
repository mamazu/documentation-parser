<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator\JSON;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use PhpSpec\ObjectBehavior;

class JsonValidatorSpec extends ObjectBehavior
{
	public function it_validates_a_valid_block(Block $block): void
	{
		$block->getContent()->willReturn('{}');

		$result = $this->validate($block);
		$result->shouldHaveCount(0);
	}

	public function it_validates_an_invalid_block(Block $block): void
	{
		$block->getContent()->willReturn('{abc}');
		$block->getFileName()->willReturn('test.json');
		$block->getRelativeLineNumber()->willReturn(0);
		$block->getType()->willReturn('json');

		$result = $this->validate($block);
		$result->shouldHaveCount(1);
		$result[0]->shouldBeLike(new Error('test.json', 1, 'Syntax error', 'json'));
	}
}
