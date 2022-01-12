<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Parser;

use PhpSpec\ObjectBehavior;

class BlockSpec extends ObjectBehavior
{
	public function let(): void
	{
		$this->beConstructedWith('some_file.php', 'Content', 10, 'php');
	}

	public function it_has_a_file_name(): void
	{
		$this->getFileName()->shouldReturn('some_file.php');
	}

	public function it_has_a_content(): void
	{
		$this->getContent()->shouldReturn('Content');
	}

	public function it_has_a_relative_line_number(): void
	{
		$this->getRelativeLineNumber()->shouldReturn(10);
	}

	public function it_has_a_type(): void
	{
		$this->getType()->shouldReturn('php');
	}
}
