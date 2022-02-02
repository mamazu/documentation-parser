<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Parser\Parser;

use PhpSpec\ObjectBehavior;

class IgnoredFileParserSpec extends ObjectBehavior
{
	public function it_parses_the_files_with_extensions(): void
	{
		$parser = $this->beConstructedWith(['png']);

		$this->canParse('hello.png')->shouldReturn(true);
		$this->canParse('hello.jpg')->shouldReturn(false);
	}

	public function it_does_not_return_any_blocks(): void
	{
		$this->parse('hello.png')->shouldReturn([]);
	}
}
